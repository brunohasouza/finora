<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $categories =  $user->categories()
            ->latest()
            ->filter(request(['search', 'type']))
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Dashboard/CategoryPage', [
            'categories' => $categories,
            'searchTerm' => $request->search,
            'typeFilter' => $request->type,
        ]);
    }
}
