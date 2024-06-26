<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrance;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EntranceController extends Controller {
    // GET all entrances
    public function index() {
        $entrances = Entrance::with(['project', 'product'])->latest()->get();
        return response()->json($entrances);
    }
  
    public function GetCountMonthEntrance() {
        $entrances = Entrance::with(['project', 'product'])
            ->whereMonth('created_at', now()->month)
            ->latest()
            ->get();
        return response()->json($entrances);
    }

     
    public function PostBetween(Request $request) {
        $start_date = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->startOfDay();
        $end_date = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->endOfDay();
    
        $entrances = Entrance::with(['project', 'product'])
            ->whereBetween('created_at', [$start_date, $end_date])
            ->latest()
            ->get();
    
        return response()->json($entrances);
    }

    public function GetProductEntrance() {
        // Obtener el producto con la mayor cantidad de entradas (sumando las cantidades)
        $productWithMostQuantity = DB::table('entrances')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->first();
    
        // Verificar si se encontró algún producto
        if ($productWithMostQuantity) {
            $product = Product::find($productWithMostQuantity->product_id);
            return response()->json([
                'product_id' => $product->id,
                'name' => $product->name,
                'total_quantity' => number_format($productWithMostQuantity->total_quantity, 0, '.', ',')

            ], 200);
        } else {
            return response()->json(['message' => 'No products found'], 404);
        }
    }
    

    public function GetEntrancesCount() {
        // Obtener la cantidad total de entradas
        $entrancesCount = Entrance::count();

        return response()->json(['count' => $entrancesCount], 200);
    }

    public function GetEntrancesCountMonthNumber() {
        // Obtener la cantidad total de entradas del mes actual
        $entrancesCount = Entrance::whereMonth('created_at', now()->month)->count();

        return response()->json(['count' => $entrancesCount], 200);
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
                    ->orWhere('entrances.folio', 'like', "%{$search}%")
                    ->orWhere('entrances.price', 'like', "%{$search}%")
                    ->orWhere('projects.name', 'like', "%{$search}%")
                    ->orWhere('products.name', 'like', "%{$search}%")
                    ->orWhere('products.location', 'like', "%{$search}%")
                    ->orWhere('entrances.project_id', 'like', "%{$search}%")
                    ->orWhere('entrances.product_id', 'like', "%{$search}%")
                    ->orWhereRaw('REPLACE(FORMAT(entrances.price * entrances.quantity, 0), ",", "") like ?', ["%{$search}%"]);

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
            'folio' => 'nullable|string|max:100',
            'price' => 'nullable|numeric'
        ]);
    
        // Obtener el producto de la base de datos
        $product = Product::findOrFail($request->product_id);
    
        // Usar el precio del producto si no se proporciona uno nuevo
        $price = $request->price ?? $product->price;
    
        // Actualizar la cantidad en la tabla de productos
        $product->quantity += $request->quantity;
        $product->save();
    
        // Crear la entrada en la tabla de entradas con el precio correcto
        $entranceData = $request->all();
        $entranceData['price'] = $price;
    
        $entrance = Entrance::create($entranceData);
    
        return response()->json($entrance, 201);
    }
    
}
