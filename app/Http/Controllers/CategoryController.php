<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // GET all categories with pagination
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 100);
        $categories = Category::latest()->paginate($perPage);
        
        return response()->json([
            'data' => $categories->items(),
            'total' => $categories->total(),
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'per_page' => $categories->perPage()
        ]);
    }

    public function SearchCategory(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 100);

        $query = Category::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('materials', 'like', '%' . $search . '%');
        }

        $categories = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => $categories->items(),
            'total' => $categories->total(),
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'per_page' => $categories->perPage()
        ]);
    }

    // GET a single category by id
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    }

    // POST a new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'materials' => 'nullable|string|max:500'
        ]);

        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    // PUT or PATCH update a category
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'materials' => 'nullable|string|max:500'
        ]);

        $category->update($request->all());
        return response()->json($category);
    }

    // DELETE a category
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Verificar si la categoría está relacionada con otros registros
        if ($category->products()->exists()) {
            return response()->json(['message' => 'La categoría está relacionada con productos y no puede ser eliminada'], 409);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}