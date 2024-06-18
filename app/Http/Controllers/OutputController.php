<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Output;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OutputController extends Controller {
    // GET all outputs
    public function index() {
        $outputs = Output::with(['project', 'product'])->latest()->get();
        return response()->json($outputs);
    }

    // GET the total number of outputs
    public function GetCountMonthOutput() {
        $outputs = Output::with(['product'])
            ->whereMonth('created_at', now()->month)
            ->latest()
            ->get();
        return response()->json($outputs);
    }

    public function GetProductOutput() {
        // Obtener el producto con la mayor cantidad de salidas (sumando las cantidades)
        $productWithMostOutput = DB::table('outputs')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->first();
    
        // Verificar si se encontró algún producto
        if ($productWithMostOutput) {
            $product = Product::find($productWithMostOutput->product_id);
            return response()->json([
                'product' => $product,
                'name' => $product->name,
                'total_quantity' => number_format($productWithMostOutput->total_quantity, 0,  '.', ',')
            ], 200);
        } else {
            return response()->json(['message' => 'No products found'], 404);
        }
    }

    // GET the total number of outputs
    public function GetOutputsCount() {
        // Obtener la cantidad total de salidas
        $outputsCount = Output::count();

        return response()->json(['count' => $outputsCount], 200);
    }
    
    // GET the total number of outputs of the current month
    public function GetOutputsCountMonthNumber() {
        // Obtener la cantidad total de salidas del mes actual
        $outputsCount = Output::whereMonth('created_at', now()->month)->count();

        return response()->json(['count' => $outputsCount], 200);
    }

    public function SearchOutput(Request $request) {
        // Obtener el parámetro de búsqueda desde la solicitud
        $search = $request->input('search');

        // Crear la consulta base con las relaciones
        $query = Output::with(['project', 'product'])
            ->leftJoin('projects', 'outputs.project_id', '=', 'projects.id')
            ->leftJoin('products', 'outputs.product_id', '=', 'products.id')
            ->select('outputs.*');

        // Si el parámetro de búsqueda está presente, filtrar las salidas
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('outputs.responsible', 'like', "%{$search}%")
                    ->orWhere('outputs.quantity', 'like', "%{$search}%")
                    ->orWhere('outputs.description', 'like', "%{$search}%")
                    ->orWhere('outputs.created_at', 'like', "%{$search}%")
                    ->orWhere('projects.name', 'like', "%{$search}%")
                    ->orWhere('products.name', 'like', "%{$search}%")
                    ->orWhere('products.location', 'like', "%{$search}%")
                    ->orWhere('outputs.project_id', 'like', "%{$search}%")
                    ->orWhere('outputs.product_id', 'like', "%{$search}%");
            });
        }

        // Ejecutar la consulta
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
            'project_id' => 'nullable|exists:projects,id',
            'product_id' => 'required|exists:products,id',
            'responsible' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string|max:100',
        ]);

        // Buscar el producto
        $product = Product::findOrFail($request->product_id);

        // Verificar si hay suficiente cantidad disponible
        if ($product->quantity < $request->quantity) {
            return response()->json(['error' => 'No hay suficiente cantidad disponible. Cantidad disponible: ' . number_format($product->quantity, 0, '.', ',')], 400);
        }

        // Deducción de la cantidad en la tabla de productos
        $product->quantity -= $request->quantity;
        $product->save();

        // Crear la salida en la tabla de salidas
        $outputData = $request->only(['project_id', 'product_id', 'responsible', 'quantity', 'description']);

        // Eliminar el campo 'project_id' si está vacío o nulo
        if (empty($outputData['project_id'])) {
            $outputData['project_id'] = null;
        }

        $output = Output::create($outputData);

        return response()->json($output, 201);
    }
}
