<?php

namespace App\Http\Controllers;

use App\Enums\CategoryTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
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

    public function list()
    {
        $categories = Auth::user()->categories()->get();
        return response()->json($categories);
    }

    public function create()
    {
        // render creation page
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => ['required','string', 'max:255'],
            'type' => ['required', new Enum(CategoryTypes::class)],
            'color' => ['required', 'string', 'max:7'],
        ]);

        $request->user()->categories()->create($fields);

        return redirect()->route('categories.index', request()->query())->with('success', 'Categoria criada com sucesso.');
    }

    public function show($id)
    {
        // render details page
    }

    public function edit($id)
    {
        // render editing page
    }

    public function update(Request $request, $id)
    {
        $fields = $request->validate([
            'name' => ['required','string', 'max:255'],
            'type' => ['required', new Enum(CategoryTypes::class)],
            'color' => ['required', 'string', 'max:7'],
        ]);

        $category = $request->user()->categories()->findOrFail($id);
        $category->update($fields);

        return redirect()->route('categories.index', request()->query())->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $category = Auth::user()->categories()->findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index', request()->query())->with('success', 'Categoria excluída com sucesso.');
    }
}
