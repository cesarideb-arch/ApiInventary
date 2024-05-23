<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrance;
use App\Models\Product;

class EntranceController extends Controller {
    // GET all entrances
    public function index() {
        $entrances = Entrance::with(['project', 'product'])->latest()->get();
        return response()->json($entrances);
    }



    public function SearchEntrance(Request $request) {
        // Obtener el parámetro de búsqueda desde la solicitud
        $search = $request->input('search');

        // Crear la consulta base con las relaciones
        $query = Entrance::with(['project', 'product'])
            ->leftJoin('projects', 'entrances.project_id', '=', 'projects.id')
            ->leftJoin('products', 'entrances.product_id', '=', 'products.id')
            ->select('entrances.*');

        // Si el parámetro de búsqueda está presente, filtrar las entradas
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('entrances.responsible', 'like', "%{$search}%")
                    ->orWhere('entrances.quantity', 'like', "%{$search}%")
                    ->orWhere('entrances.description', 'like', "%{$search}%")
                    ->orWhere('entrances.created_at', 'like', "%{$search}%")
                    ->orWhere('projects.name', 'like', "%{$search}%")
                    ->orWhere('products.name', 'like', "%{$search}%")
                    ->orWhere('entrances.project_id', 'like', "%{$search}%")
                    ->orWhere('entrances.product_id', 'like', "%{$search}%");
            });
        }

        // Ejecutar la consulta
        $entrances = $query->get();

        return response()->json($entrances);
    }



    // GET a single entrance by id
    public function show($id) {
        $entrance = Entrance::with(['project', 'product'])->find($id);
        if (!$entrance) {
            return response()->json(['message' => 'Entrance not found'], 404);
        }
        return response()->json($entrance);
    }

    // POST a new entrance
    public function store(Request $request) {
        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'product_id' => 'required|exists:products,id',
            'responsible' => 'required|string|max:100',
            'quantity' => 'required|integer',
            'description' => 'nullable|string|max:100',
        ]);

        // Actualizar la cantidad en la tabla de productos
        $product = Product::findOrFail($request->product_id);
        $product->quantity += $request->quantity;
        $product->save();

        // Crear la entrada en la tabla de entradas
        $entrance = Entrance::create($request->all());

        return response()->json($entrance, 201);
    }
}
