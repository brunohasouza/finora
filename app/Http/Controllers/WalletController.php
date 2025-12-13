<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Bank;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
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

        return Inertia::render('Dashboard/WalletPage', [
            'accounts' => $wallets,
            'totalBalance' => $totalBalance,
        ]);
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
            'name' => ['required','string', 'max:255'],
            'bank_id' => ['required', 'integer', 'exists:banks,id'],
            'balance' => ['required', 'integer', 'max_digits:12'],
        ]);
        
        $bank = Bank::findOrFail($request->bank_id);
        $request->user()->wallets()->create([
            'name' => $fields['name'],
            'bank_id' => $bank->id,
            'balance' => $fields['balance'],
        ]);

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
            'name' => ['required','string', 'max:255'],
            'bank_id' => ['required', 'integer', 'exists:banks,id'],
            'balance' => ['required', 'integer', 'max_digits:12'],
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
