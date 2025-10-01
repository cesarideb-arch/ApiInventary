<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category; // Import the Category model
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Supplier; // Import the Supplier model
use App\Models\Project; // Import the Project model
use Exception;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller {
    /**
     * Devuelve todos los productos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $products = Product::with(['category', 'Supplier'])
            ->withCount(['loans' => function($query) {
                $query->where('status', 1); // Solo préstamos activos
            }])
            ->latest()
            ->get();
        return response()->json($products, 200);
    }

    // conteo de productos
    public function getCountProducts() {
        $count = Product::count();
        $formattedCount = number_format($count, 0, '.', ',');
        return response()->json(['count' => $formattedCount], 200);
    }

    // GET de categorias y proveedores

    public function getCategoryProducts() {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('company')->get();

        return response()->json([
            'categories' => $categories,
            'suppliers' => $suppliers
        ], 200);
    }

    // GET de proyectos
    public function getprojects() {
        $projects = Project::orderBy('name')->get();

        return response()->json($projects, 200);
    }

    public function SearchGet(Request $request) {
        // Obtener el parámetro de búsqueda desde la solicitud
        $search = $request->input('search');
        $categories = Category::orderBy('id')->get();
        $suppliers = Supplier::orderBy('id')->get();

        // Crear la consulta base con las relaciones
        $query = Product::with(['category', 'supplier'])
            ->withCount(['loans' => function($query) {
                $query->where('status', 1); // Solo préstamos activos
            }])
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->select('products.*');

        // Si el parámetro de búsqueda está presente, filtrar los productos
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'LIKE', "%$search%")
                    ->orWhere('products.category_id', 'LIKE', "%$search%")
                    ->orWhere('products.price', 'LIKE', "%$search%")
                    ->orWhere('products.location', 'LIKE', "%$search%")
                    ->orWhere('products.description', 'LIKE', "%$search%")
                    ->orWhere('products.observations', 'LIKE', "%$search%")
                    ->orWhere('products.quantity', 'LIKE', "%$search%")
                    ->orWhere('categories.name', 'LIKE', "%$search%")
                    ->orWhere('suppliers.company', 'LIKE', "%$search%");
            });
        }

        // Ejecutar la consulta y ordenar por la fecha de creación
        $products = $query->latest('products.created_at')->get();

        return response()->json($products, 200);
    }

    /**
     * Crea un nuevo producto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // Validar los datos de entrada
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'model' => 'nullable|string|max:50',
                'measurement_unit' => 'nullable|string|max:15',
                'brand' => 'nullable|string|max:50',
                'quantity' => 'required|integer',
                'description' => 'nullable|string',
                'price' => 'required|numeric|between:0,999999.99',
                'profile_image' => 'nullable|file|max:2048|mimes:jpeg,png,gif,svg',
                'serie' => 'nullable|string|max:40',
                'observations' => 'nullable|string|max:50',
                'location' => 'nullable|string|max:20',
                'category_id' => 'required|exists:categories,id',
                'supplier_id' => 'nullable|exists:suppliers,id',
            ]);

            // Comprobar si la solicitud contiene una imagen
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $extension = $file->getClientOriginalExtension();
                $new_name = time() . '_1.' . $extension;

                // Mover la nueva imagen a la carpeta public/images
                $file->move(public_path('images'), $new_name);

                // Ruta completa de la nueva imagen
                $imagePath = 'images/' . $new_name;

                // Asignar la ruta de la nueva imagen al campo profile_image
                $validated['profile_image'] = $imagePath;
            }

            // Crear un nuevo producto con los datos validados
            $product = Product::create($validated);

            // Devolver una respuesta JSON con el producto creado
            return response()->json($product, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    /**
     * Devuelve un producto específico según su ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function show($id) {
        $product = Product::withCount(['loans' => function($query) {
            $query->where('status', 1); // Solo préstamos activos
        }])->find($id);
        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        return response()->json($product, 200);
    }

    /**
     * Actualiza un producto existente según su ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request, $id) {
        // Buscar el producto a actualizar
        $product = Product::findOrFail($id);

        // Validar los datos de entrada
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'model' => 'nullable|string|max:50',
            'measurement_unit' => 'nullable|string|max:15',
            'brand' => 'nullable|string|max:50',
            'quantity' => 'required|integer',
            'description' => 'nullable|string',
            'price' => 'required|numeric|between:0,999999.99',
            'profile_image' => 'nullable|file|max:2048|mimes:jpeg,png,gif,svg',
            'serie' => 'nullable|string|max:40',
            'observations' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        // Comprobar si la solicitud contiene una imagen
        if ($request->hasFile('profile_image')) {
            // Eliminar la imagen anterior si existe
            if (File::exists(public_path($product->profile_image))) {
                File::delete(public_path($product->profile_image));
            }

            // Procesar la nueva imagen
            $file = $request->file('profile_image');
            $extension = $file->getClientOriginalExtension();
            $new_name = time() . '_1.' . $extension;

            // Mover la nueva imagen a la carpeta public/images
            $file->move(public_path('images'), $new_name);

            // Ruta completa de la nueva imagen
            $imagePath = 'images/' . $new_name;

            // Asignar la ruta de la nueva imagen al campo profile_image
            $validated['profile_image'] = $imagePath;
        }

        // Actualizar el producto con los datos validados
        $product->update($validated);

        // Devolver una respuesta JSON con el producto actualizado
        return response()->json($product, 200);
    }
    /**
     * Elimina un producto existente según su ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     public function destroy($id) {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
    
        DB::beginTransaction();
    
        try {
            // Elimina el producto primero
            $product->delete();
    
            // Intenta eliminar la imagen si existe
            if ($product->profile_image) {
                $imagePath = public_path($product->profile_image);
                if (File::exists($imagePath)) {
                    if (!File::delete($imagePath)) {
                        // Si la imagen no se puede eliminar, lanza una excepción
                        throw new Exception('No se pudo eliminar la imagen');
                    }
                }
            }
    
            DB::commit();
            return response()->json(['message' => 'Producto eliminado exitosamente'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
}