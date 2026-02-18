<?php

namespace App\Observers;

use App\Enums\CategoryTypes;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $wallet = $transaction->wallet;

        if ($wallet->isCreditCard()) {
            $invoice = $this->findOrCreateInvoice($wallet, $transaction->date);
            $transaction->updateQuietly(['invoice_id' => $invoice->id]);
            $this->applyToInvoice($invoice, $transaction->type, $transaction->amount);
        } else {
            $this->applyToBalance($wallet, $transaction->type, $transaction->amount);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $originalWalletId = $transaction->getOriginal('wallet_id');
        $originalType = $transaction->getOriginal('type');
        $originalAmount = $transaction->getOriginal('amount');
        $originalWallet = Wallet::find($originalWalletId);

        // Revert original
        if ($originalWallet) {
            if ($originalWallet->isCreditCard()) {
                $originalInvoiceId = $transaction->getOriginal('invoice_id');
                $originalInvoice = Invoice::find($originalInvoiceId);
                if ($originalInvoice) {
                    $this->revertFromInvoice($originalInvoice, $originalType, $originalAmount);
                }
            } else {
                $this->revertFromBalance($originalWallet, $originalType, $originalAmount);
            }
        }

        // Apply new
        $wallet = $transaction->wallet;

        if ($wallet->isCreditCard()) {
            $invoice = $this->findOrCreateInvoice($wallet, $transaction->date);
            $transaction->updateQuietly(['invoice_id' => $invoice->id]);
            $this->applyToInvoice($invoice, $transaction->type, $transaction->amount);
        } else {
            $transaction->updateQuietly(['invoice_id' => null]);
            $this->applyToBalance($wallet, $transaction->type, $transaction->amount);
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $wallet = $transaction->wallet;

        if ($wallet->isCreditCard() && $transaction->invoice) {
            $this->revertFromInvoice($transaction->invoice, $transaction->type, $transaction->amount);
        } else {
            $this->revertFromBalance($wallet, $transaction->type, $transaction->amount);
        }
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        $wallet = $transaction->wallet;

        if ($wallet->isCreditCard() && $transaction->invoice) {
            $this->applyToInvoice($transaction->invoice, $transaction->type, $transaction->amount);
        } else {
            $this->applyToBalance($wallet, $transaction->type, $transaction->amount);
        }
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        $wallet = $transaction->wallet;

        if ($wallet->isCreditCard() && $transaction->invoice) {
            $this->revertFromInvoice($transaction->invoice, $transaction->type, $transaction->amount);
        } else {
            $this->revertFromBalance($wallet, $transaction->type, $transaction->amount);
        }
    }

    /**
     * Apply transaction to wallet balance (checking accounts).
     */
    private function applyToBalance(Wallet $wallet, string $type, int $amount): void
    {
        if ($type === CategoryTypes::INCOME->value) {
            $wallet->increment('balance', $amount);
        } else {
            $wallet->decrement('balance', $amount);
        }
    }

    /**
     * Revert transaction from wallet balance (checking accounts).
     */
    private function revertFromBalance(Wallet $wallet, string $type, int $amount): void
    {
        if ($type === CategoryTypes::INCOME->value) {
            $wallet->decrement('balance', $amount);
        } else {
            $wallet->increment('balance', $amount);
        }
    }

    /**
     * Apply transaction to invoice total (credit cards).
     */
    private function applyToInvoice(Invoice $invoice, string $type, int $amount): void
    {
        if ($type === CategoryTypes::EXPENSE->value) {
            $invoice->increment('total', $amount);
        } else {
            $invoice->decrement('total', $amount);
        }
    }

    /**
     * Revert transaction from invoice total (credit cards).
     */
    private function revertFromInvoice(Invoice $invoice, string $type, int $amount): void
    {
        if ($type === CategoryTypes::EXPENSE->value) {
            $invoice->decrement('total', $amount);
        } else {
            $invoice->increment('total', $amount);
        }
    }

    /**
     * Find or create the appropriate invoice for a credit card transaction.
     */
    private function findOrCreateInvoice(Wallet $wallet, string $transactionDate): Invoice
    {
        $date = Carbon::parse($transactionDate);
        $closingDay = $wallet->closing_day;
        $dueDay = $wallet->due_day;

        // If the transaction is after the closing day, it belongs to the next month's invoice
        if ($date->day > $closingDay) {
            $closingDate = $date->copy()->addMonth()->day($closingDay);
        } else {
            $closingDate = $date->copy()->day($closingDay);
        }

        $referenceDate = $closingDate->copy()->startOfMonth();

        // Due date is in the month after closing
        $dueDate = $closingDate->copy()->addMonth()->day($dueDay);

        return Invoice::firstOrCreate(
            [
                'wallet_id' => $wallet->id,
                'reference_date' => $referenceDate->toDateString(),
            ],
            [
                'closing_date' => $closingDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'total' => 0,
                'status' => InvoiceStatus::OPEN->value,
            ],
        );
    }
}
