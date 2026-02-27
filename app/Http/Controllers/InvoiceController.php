<?php

namespace App\Http\Controllers;

use App\Enums\CategoryTypes;
use App\Enums\InvoiceStatus;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $invoices = Invoice::whereHas('wallet', fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Dashboard/InvoicePage', [
            'invoices' => $invoices,
        ]);
    }

    public function show(Invoice $invoice)
    {
        $user = Auth::user();

        if ($invoice->wallet->user_id !== $user->id) {
            abort(403);
        }

        $transactions = $invoice->transactions()
            ->latest('date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Dashboard/InvoiceShowPage', [
            'invoice' => $invoice,
            'transactions' => $transactions,
        ]);
    }

    public function pay(Request $request, Invoice $invoice)
    {
        $user = Auth::user();

        if ($invoice->wallet->user_id !== $user->id) {
            abort(403);
        }

        if (!$invoice->isClosed()) {
            throw ValidationException::withMessages([
                'invoice' => 'Apenas faturas fechadas podem ser pagas.',
            ]);
        }

        $validated = $request->validate([
            'wallet_id' => ['required', 'integer', 'exists:wallets,id'],
        ]);

        $wallet = Wallet::findOrFail($validated['wallet_id']);

        if ($wallet->user_id !== $user->id || $wallet->isCreditCard()) {
            throw ValidationException::withMessages([
                'wallet_id' => 'Selecione uma conta corrente válida.',
            ]);
        }

        $category = Category::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Pagamento de Fatura', 'system' => true],
            ['type' => CategoryTypes::EXPENSE->value, 'color' => '#6366f1'],
        );

        Transaction::create([
            'description' => "Pgto fatura {$invoice->wallet->name} {$invoice->reference_date->format('m/Y')}",
            'amount'      => $invoice->total,
            'date'        => now()->toDateString(),
            'type'        => CategoryTypes::EXPENSE->value,
            'category_id' => $category->id,
            'wallet_id'   => $wallet->id,
            'user_id'     => $user->id,
        ]);

        $invoice->update(['status' => InvoiceStatus::PAID->value]);

        return redirect()->back();
    }
}
