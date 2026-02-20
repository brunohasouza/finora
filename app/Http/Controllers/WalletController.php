<?php

namespace App\Http\Controllers;

use App\Enums\WalletTypes;
use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $wallets = $user->wallets()->with('bank')->get();
        $totalBalance = $wallets->sum('balance');
        $totalCreditLimit = $wallets->sum('credit_limit');
        $totalAvailableLimit = $wallets->sum('available_limit');

        return Inertia::render('Dashboard/WalletPage', [
            'accounts' => $wallets,
            'totalBalance' => $totalBalance,
            'totalCreditLimit' => $totalCreditLimit,
            'totalAvailableLimit' => $totalAvailableLimit,
        ]);
    }

    public function list()
    {
        $accounts = Auth::user()->wallets()->get();
        return response()->json($accounts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWalletRequest $request)
    {
        $fields = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(WalletTypes::class)],
            'bank_id' => ['required', 'integer', 'exists:banks,id'],
            'balance' => ['nullable', 'integer', 'max_digits:12'],
            'credit_limit' => ['nullable', 'integer', 'max_digits:12'],
            'closing_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'due_day' => ['nullable', 'integer', 'min:1', 'max:31'],
        ]);

        $request->user()->wallets()->create($fields);

        return redirect()->route('accounts.index', request()->query())->with('success', 'Conta adicionada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWalletRequest $request, $id)
    {
        $fields = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(WalletTypes::class)],
            'bank_id' => ['required', 'integer', 'exists:banks,id'],
            'balance' => ['nullable', 'integer', 'max_digits:12'],
            'credit_limit' => ['nullable', 'integer', 'max_digits:12'],
            'closing_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'due_day' => ['nullable', 'integer', 'min:1', 'max:31'],
        ]);

        $account = $request->user()->wallets()->findOrFail($id);
        $account->update($fields);

        return redirect()->route('accounts.index', request()->query())->with('success', 'Conta atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $wallet = Auth::user()->wallets()->findOrFail($id);
        $wallet->delete();

        return redirect()->route('accounts.index', request()->query())->with('success', 'Conta excluída com sucesso.');
    }
}
