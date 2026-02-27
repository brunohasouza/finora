<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
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
}
