<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Output;
use App\Models\Product;

class OutputController extends Controller {
    // GET all outputs
    public function index() {
        $outputs = Output::with(['project', 'product'])->latest()->get();
        return response()->json($outputs);
    }

    public function SearchOutput(Request $request) {
        // Obtener el parámetro de búsqueda desde la solicitud
        $search = $request->input('search');
    
        // Crear la consulta base con las relaciones
        $query = Output::with(['project', 'product'])
                        ->join('projects', 'outputs.project_id', '=', 'projects.id')
                        ->join('products', 'outputs.product_id', '=', 'products.id')
                        ->select('outputs.*');
    
        // Si el parámetro de búsqueda está presente, filtrar las entradas
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('outputs.responsible', 'like', "%{$search}%")
                  ->orWhere('outputs.quantity', 'like', "%{$search}%")
                  ->orWhere('outputs.description', 'like', "%{$search}%")
                  ->orWhere('outputs.created_at', 'like', "%{$search}%")
                  ->orWhere('projects.name', 'like', "%{$search}%")
                  ->orWhere('products.name', 'like', "%{$search}%")
                  ->orWhere('outputs.project_id', 'like', "%{$search}%")
                  ->orWhere('outputs.product_id', 'like', "%{$search}%");
            });
        } else {
            // Si no hay parámetro de búsqueda, obtener todas las salidas
            $outputs = Output::with(['project', 'product'])->get();
            return response()->json($outputs);
        }
    
        // Ejecutar la consulta si hay un parámetro de búsqueda
        $outputs = $query->get();
    
        return response()->json($outputs);
    }
    







    // GET a single output by id
    public function show($id) {
        $output = Output::with(['project', 'product'])->find($id);
        if (!$output) {
            return response()->json(['message' => 'Output not found'], 404);
        }
        return response()->json($output);
    }

    // POST a new output
    public function store(Request $request) {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'product_id' => 'required|exists:products,id',
            'responsible' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string|max:100',
            'date' => 'required|date'
        ]);
    
        // Buscar el producto
        $product = Product::findOrFail($request->product_id);
    
        // Verificar si hay suficiente cantidad disponible
        if ($product->quantity < $request->quantity) {
            return response()->json(['error' => 'No hay suficiente cantidad disponible. Cantidad disponible: ' . $product->quantity], 400);
        }
    
        // Deducción de la cantidad en la tabla de productos
        $product->quantity -= $request->quantity;
        $product->save();
    
        // Crear la salida en la tabla de salidas
        $output = Output::create($request->all());
    
        return response()->json($output, 201);
    }
}
