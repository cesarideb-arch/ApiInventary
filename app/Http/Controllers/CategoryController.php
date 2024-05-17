<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller {
    // GET all categories
    public function index() {
        $categories = Category::latest()->get();
        return response()->json($categories);
    }



    public function SearchCategory(Request $request) {
        // Obtener el parámetro de búsqueda desde la solicitud
        $search = $request->input('search');

        // Si el parámetro de búsqueda está presente, filtrar las categorías
        if ($search) {
            $categories = Category::where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->get();
        } else {
            // Si no hay parámetro de búsqueda, obtener todas las categorías
            $categories = Category::all();
        }

        return response()->json($categories);
    }












    // GET a single category by id
    public function show($id) {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    }

    // POST a new category
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:100'
        ]);

        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    // PUT or PATCH update a category
    public function update(Request $request, $id) {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'string|max:100',
            'description' => 'nullable|string|max:100'
        ]);

        $category->update($request->all());
        return response()->json($category);
    }

    // DELETE a category
    public function destroy($id) {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
