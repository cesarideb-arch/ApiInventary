<?php

namespace App\Http\Controllers;

use App\Models\Loan; // Import the Loan model class

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Product; // Import the Product model class
use Illuminate\Support\Facades\DB; // Import the DB class

class LoanController extends Controller {


    public function index() {
        $loans = Loan::with(['product'])->latest()->get();
        return response()->json($loans);
    }

    public function GetCountMonthLoan() {
        $loans = Loan::with(['product'])
            ->whereMonth('created_at', now()->month)
            ->latest()
            ->get();
        return response()->json($loans);
    }


    public function GetProductLoan() {
        // Obtener el producto con la mayor cantidad de préstamos (sumando las cantidades)
        $productWithMostLoan = DB::table('loans')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->first();

        // Verificar si se encontró algún producto
        if ($productWithMostLoan) {
            $product = Product::find($productWithMostLoan->product_id);
            return response()->json([
                'product' => $product,
                'name' => $product->name,
                'total_quantity' => number_format($productWithMostLoan->total_quantity, 0, '.', ',')
            ], 200);
        } else {
            return response()->json(['message' => 'No products found'], 404);
        }
    }


    public function SearchLoan(Request $request) {
        // Obtener el parámetro de búsqueda desde la solicitud
        $search = $request->input('search');

        // Crear la consulta base con las relaciones
        $query = Loan::with(['product'])
            ->join('products', 'loans.product_id', '=', 'products.id')
            ->select('loans.*');

        // Si el parámetro de búsqueda está presente, filtrar las entradas
        if ($search) {
            $query->where(function ($q) use ($search) {
                // Convertir el texto de búsqueda a minúsculas para comparación
                $searchLower = strtolower($search);

                // Determinar el valor del status basado en el texto de búsqueda
                $statusValue = null;
                if ($searchLower === 'producto prestado') {
                    $statusValue = 1;
                } else if ($searchLower === 'producto regresado') {
                    $statusValue = 0;
                }

                // Aplicar filtros a la consulta
                $q->where('loans.responsible', 'like', "%{$search}%")
                    ->orWhere('loans.quantity', 'like', "%{$search}%")
                    ->orWhere('loans.created_at', 'like', "%{$search}%")
                    ->orWhere('loans.updated_at', 'like', "%{$search}%")
                    ->orWhere('loans.observations', 'like', "%{$search}%")
                    ->orWhere('products.name', 'like', "%{$search}%")
                    ->orWhere('products.location', 'like', "%{$search}%")
                    ->orWhere('loans.product_id', 'like', "%{$search}%");

                // Si se ha determinado un valor de status, agregarlo a la consulta
                if ($statusValue !== null) {
                    $q->orWhere('loans.status', $statusValue);
                }
            });
        } else {
            // Si no hay parámetro de búsqueda, obtener todos los préstamos
            $loans = Loan::with(['product'])->get();
            return response()->json($loans);
        }

        // Ejecutar la consulta si hay un parámetro de búsqueda
        $loans = $query->get();

        return response()->json($loans);
    }


    public function getCount() {
        $count = Loan::where('status', 1)->count();
        return response()->json(['count' => number_format($count, 0, '.', ',')]);
    }
    public function getCountFinish() {
        $count = Loan::where('status', 0)->count();
        return response()->json(['count' => number_format($count, 0, '.', ',')]);
    }
    
    public function getCountAll() {
        $count = Loan::count();
        return response()->json(['count' => number_format($count, 0, '.', ',')]);
    }


    public function GetFinished() {
        $loans = Loan::with(['product'])
            ->where('status', 0)
            ->latest()
            ->get();
        return response()->json($loans);
    }

    public function GetStarted() {
        $loans = Loan::with(['product'])
            ->where('status', 1)
            ->latest()
            ->get();
        return response()->json($loans);
    }


// Get the total number of loans of the current month
public function GetLoanCountMonthNumber() {
    // Obtener la cantidad total de préstamos del mes actual
    $loansCount = Loan::whereMonth('created_at', now()->month)->count();

    return response()->json(['count' => $loansCount], 200);
}

    public function store(Request $request) {
        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'product_id' => 'required|exists:products,id',
            'responsible' => 'required|string|max:100',
            'quantity' => 'required|integer',
            'observations' => 'nullable|string', 
        ]);
    
        // Verificar que observations está presente en la solicitud
        if ($request->has('observations')) {
            Log::info('Observations field is present in the request: ' . $request->observations);
        } else {
            Log::info('Observations field is not present in the request.');
        }
    
        // Buscar el producto
        $product = Product::findOrFail($request->product_id);
    
        // Verificar si hay suficiente cantidad disponible
        if ($product->quantity < $request->quantity) {
            return response()->json(['error' => 'No hay suficiente cantidad disponible. Cantidad disponible: ' . number_format($product->quantity, 0, '.', ',')], 400);
        }
    
        // Deducción de la cantidad en la tabla de productos
        $product->quantity -= $request->quantity;
        $product->save();
    
        // Crear el préstamo en la tabla de préstamos
        $loanData = $request->only(['product_id', 'responsible', 'quantity', 'observations', 'project_id']);
        $loanData['status'] = 1;
        $loan = Loan::create($loanData);
    
        return response()->json($loan, 201);
    }
    


    public function show($id) {
        $loan = Loan::with(['product'])->find($id);
        if (!$loan) {
            return response()->json(['message' => 'Loan not found'], 404);
        }
        return response()->json($loan);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'responsible' => 'required|string|max:100',
            'quantity' => 'required|integer',
            'date' => 'required|date'
        ]);

        // Buscar el préstamo por su ID
        $loan = Loan::findOrFail($id);

        // Buscar el producto
        $product = Product::findOrFail($request->product_id);

        // Verificar si hay suficiente cantidad disponible
        if ($product->quantity < $request->quantity) {
            return response()->json(['error' => 'No hay suficiente cantidad disponible. Cantidad disponible: ' . number_format($product->quantity, 0, '.', ',')], 400);
        }

        // Aumentar la cantidad del producto devuelto en el inventario
        $product->quantity -= $loan->quantity;
        $product->save();

        // Actualizar el préstamo
        $loan->update($request->all());

        return response()->json($loan, 200);
    }



    public function comeBackLoan(Request $request, $id) {
        try {
            $loan = Loan::findOrFail($id);

            if ($loan->status !== 1) {
                return response()->json(['error' => 'Este préstamo ya ha sido devuelto.'], 400);
            }

            $product = Product::findOrFail($loan->product_id);
            $product->quantity += $loan->quantity;
            $product->save();

            $loan->status = 0;
            $loan->observations = $request->input('observations'); // Asegúrate de que las observaciones se guardan
            $loan->save();

            return response()->json(['message' => 'El préstamo ha sido devuelto correctamente.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar la devolución del préstamo.', 'details' => $e->getMessage()], 500);
        }
    }
}
