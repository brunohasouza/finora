<?php

namespace App\Observers;

use App\Enums\CategoryTypes;
use App\Models\Transaction;
use App\Models\Wallet;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $this->applyToBalance($transaction->wallet, $transaction->type, $transaction->amount);
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $originalWalletId = $transaction->getOriginal('wallet_id');
        $originalType = $transaction->getOriginal('type');
        $originalAmount = $transaction->getOriginal('amount');

        // Revert from original wallet
        $originalWallet = Wallet::find($originalWalletId);
        if ($originalWallet) {
            $this->revertFromBalance($originalWallet, $originalType, $originalAmount);
        }

        // Apply to current wallet
        $this->applyToBalance($transaction->wallet, $transaction->type, $transaction->amount);
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $this->revertFromBalance($transaction->wallet, $transaction->type, $transaction->amount);
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        $this->applyToBalance($transaction->wallet, $transaction->type, $transaction->amount);
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        $this->revertFromBalance($transaction->wallet, $transaction->type, $transaction->amount);
    }

    /**
     * Apply transaction to wallet balance.
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
     * Revert transaction from wallet balance.
     */
    private function revertFromBalance(Wallet $wallet, string $type, int $amount): void
    {
        if ($type === CategoryTypes::INCOME->value) {
            $wallet->decrement('balance', $amount);
        } else {
            $wallet->increment('balance', $amount);
        }
    }
}
