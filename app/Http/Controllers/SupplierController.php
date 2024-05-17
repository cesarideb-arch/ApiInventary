<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Schema; // Necesario para usar Schema

class SupplierController extends Controller {
    // GET all suppliers
    public function index() {
        $suppliers = Supplier::latest()->get();
        return response()->json($suppliers);
    }


    public function SearchSupplier(Request $request) {
        // Obtener el parámetro de búsqueda desde la solicitud
        $search = $request->input('search');

        // Si el parámetro de búsqueda está presente, filtrar las categorías
        if ($search) {
            $suppliers = Supplier::where('article', 'like', "%{$search}%")
                ->orWhere('company', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->get();
        } else {
            // Si no hay parámetro de búsqueda, obtener todos los proveedores
            $suppliers = Supplier::all();
        }

        return response()->json($suppliers);
    }




    // GET a single supplier by id
    public function show($id) {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
        return response()->json($supplier);
    }

    // POST a new supplier
    public function store(Request $request) {
        $request->validate([
            'article' => 'required|string|max:255',
            'price' => 'required|numeric',
            'company' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:100' // Nueva validación para el campo address
        ]);

        $supplier = Supplier::create($request->all());
        return response()->json($supplier, 201);
    }

    // PUT or PATCH update a supplier
    public function update(Request $request, $id) {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $request->validate([
            'article' => 'string|max:255',
            'price' => 'numeric',
            'company' => 'string|max:255',
            'phone' => 'string|max:255',
            'email' => 'email|max:255',
            'address' => 'string|max:100' // Nueva validación para el campo address
        ]);

        $supplier->update($request->all());
        return response()->json($supplier);
    }

    // DELETE a supplier
    public function destroy($id) {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
    
        // Verificar si el proveedor está relacionado con otros registros (por ejemplo, con productos)
        if ($supplier->products()->exists()) { // Cambia 'products' por la relación adecuada
            return response()->json(['message' => 'La proveedor está relacionada con productos y no puede ser eliminada'], 400);
        }
    
        $supplier->delete();
        return response()->json(['message' => 'Supplier deleted successfully']);
    }
    
}
