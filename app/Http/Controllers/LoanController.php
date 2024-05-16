<?php

namespace App\Http\Controllers;

use App\Models\Loan; // Import the Loan model class

use Illuminate\Http\Request;
use App\Models\Product; // Import the Product model class

class LoanController extends Controller {


    public function index() {
        $loans = Loan::with(['product'])->latest()->get();
        return response()->json($loans);
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
                $q->where('loans.responsible', 'like', "%{$search}%")
                    ->orWhere('loans.quantity', 'like', "%{$search}%")
                    ->orWhere('loans.created_at', 'like', "%{$search}%")
                    ->orWhere('loans.status', 'like', "%{$search}%")
                    ->orWhere('products.name', 'like', "%{$search}%")
                    ->orWhere('loans.product_id', 'like', "%{$search}%");
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
        return response()->json(['count' => $count]);
    }

    public function store(Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'responsible' => 'required|string|max:100',
            'quantity' => 'required|integer',
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


        // Crear el préstamo en la tabla de préstamos
        $loan = Loan::create($request->all() + ['status' => 1]); // Use the Loan model class and set the status to 1

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
            return response()->json(['error' => 'No hay suficiente cantidad disponible.'], 400);
        }

        // Aumentar la cantidad del producto devuelto en el inventario
        $product->quantity -= $loan->quantity;
        $product->save();

        // Actualizar el préstamo
        $loan->update($request->all());

        return response()->json($loan, 200);
    }



    public function comeBackLoan($id) {
        // Buscar el préstamo por su ID
        $loan = Loan::findOrFail($id);

        // Verificar si el préstamo está en estado activo (status = 1)
        if ($loan->status !== 1) {
            return response()->json(['error' => 'Este préstamo ya ha sido devuelto.'], 400);
        }

        // Aumentar la cantidad del producto devuelto en el inventario
        $product = Product::findOrFail($loan->product_id);
        $product->quantity += $loan->quantity;
        $product->save();

        // Actualizar el estado del préstamo a devuelto (status = 0)
        $loan->status = 0;
        $loan->save();

        return response()->json(['message' => 'El préstamo ha sido devuelto correctamente.'], 200);
    }
}
