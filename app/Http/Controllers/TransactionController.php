<?php

namespace App\Http\Controllers;

use App\Enums\CategoryTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $wallets = $user->wallets()->with('bank')->get();
        $totalBalance = $wallets->sum('balance');

        $transactions = $user->transactions()
            ->latest('created_at')
            ->filter(request(['type', 'category_id']))
            ->paginate(10)
            ->withQueryString();

        $expenses = $user->transactions()
            ->where('type', CategoryTypes::EXPENSE)
            ->sum('amount');

        $incomes = $user->transactions()
            ->where('type', CategoryTypes::INCOME)
            ->sum('amount');

        return Inertia::render('HomePage', [
            'transactions' => $transactions,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'balance' => [
                'total' => $totalBalance,
                'expenses' => $expenses,
                'incomes' => $incomes,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'type' => ['required', new Enum(CategoryTypes::class)],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'wallet_id' => ['required', 'integer', 'exists:wallets,id'],
        ]);

        $request->user()->transactions()->create($fields);

        return redirect()->route('home', request()->query())->with('success', 'Transação criada com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $fields = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'type' => ['required', new Enum(CategoryTypes::class)],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'wallet_id' => ['required', 'integer', 'exists:wallets,id'],
        ]);

        $transaction = $request->user()->transactions()->findOrFail($id);
        $transaction->update($fields);

        return redirect()->route('home', request()->query())->with('success', 'Transação atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $transaction = Auth::user()->transactions()->findOrFail($id);
        $transaction->delete();

        return redirect()->route('home', request()->query())->with('success', 'Transação excluída com sucesso.');
    }
}
