<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->has('status')) {
            if ($request->status === '1' || $request->status === '0') {
                $query->where('status', $request->status);
            }
        }

        // Sort results
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        $categories = $query->paginate(10);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Using modal instead of separate create view
        return redirect()->route('categories.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('categories.index')
                ->withErrors($validator)
                ->withInput();
        }

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::with(['products' => function($query) {
            $query->where('status', true);
        }])->findOrFail($id);

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Using modal instead of separate edit view
        return redirect()->route('categories.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('categories.index')
                ->withErrors($validator)
                ->withInput();
        }

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->has('status') ? $request->status : $category->status,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has products
        $productsCount = Product::where('category_id', $id)->count();
        
        if ($productsCount > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados. Desactive la categoría en su lugar.');
        }
        
        $category->delete();
        
        return redirect()->route('categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }



}
