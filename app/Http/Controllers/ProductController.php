<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Devuelve todos los productos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products, 200);
    }

    /**
     * Crea un nuevo producto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    // Validar los datos de entrada
    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'model' => 'nullable|string|max:100',
        'measurement_unit' => 'nullable|string|max:10',
        'brand' => 'nullable|string|max:100',
        'quantity' => 'required|integer',
        'description' => 'nullable|string',
        'price' => 'required|numeric|between:0,999999.99',
        'product_image' => 'nullable|file|max:2048|mimes:jpeg,png,gif,svg', // Asegúrate de que el archivo tiene un tamaño máximo de 2MB y es de tipo imagen (jpeg, png, gif, svg)
        'category_id' => 'nullable|exists:categories,id',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'serie' => 'nullable|string|max:100',
        'observations' => 'nullable|string|max:255',
        'location' => 'nullable|string|max:100',
    ]);

    // Comprobar si la solicitud contiene una imagen
    if ($request->hasFile('product_image')) {
        $file = $request->file('product_image');
        $extension = $file->getClientOriginalExtension();
        $new_name = time() . '_1.' . $extension;
        
        // Mover la imagen a la carpeta public/images
        $file->move(public_path('images'), $new_name);
        
        // Ruta completa de la imagen
        $imagePath = 'images/' . $new_name;
        
        // Asignar la ruta de la imagen al campo product_image
        $validated['product_image'] = $imagePath;
    }

    // Crear el producto utilizando los datos validados
    $product = Product::create($validated);

    // Devolver una respuesta JSON con el producto creado y un código de estado 201
    return response()->json($product, 201);
}
    /**
     * Devuelve un producto específico según su ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function show($id)
    {
        $product = Product::find($id);
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
   
   
     public function update(Request $request, $id)
{
    // Buscar el producto a actualizar
    $product = Product::findOrFail($id);

    // Validar los datos de entrada
    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'model' => 'nullable|string|max:100',
        'measurement_unit' => 'nullable|string|max:10',
        'brand' => 'nullable|string|max:100',
        'quantity' => 'required|integer',
        'description' => 'nullable|string',
        'price' => 'required|numeric|between:0,999999.99',
        'product_image' => 'nullable|file|max:2048|mimes:jpeg,png,gif,svg', // Asegúrate de que el archivo tiene un tamaño máximo de 2MB y es de tipo imagen (jpeg, png, gif, svg)
        'category_id' => 'nullable|exists:categories,id',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'serie' => 'nullable|string|max:100',
        'observations' => 'nullable|string|max:255',
        'location' => 'nullable|string|max:100',
    ]);

    // Comprobar si la solicitud contiene una imagen
    if ($request->hasFile('product_image')) {
        if (File::exists(public_path($product->product_image))) {
            // Eliminar la imagen anterior si existe
            File::delete(public_path($product->product_image));
        }

        $file = $request->file('product_image');
        $extension = $file->getClientOriginalExtension();
        $new_name = time() . '_1.' . $extension;

        // Mover la nueva imagen a la carpeta public/images
        $file->move(public_path('images'), $new_name);

        // Ruta completa de la nueva imagen
        $imagePath = 'images/' . $new_name;

        // Asignar la ruta de la nueva imagen al campo product_image
        $validated['product_image'] = $imagePath;
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
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        $product->delete();
        return response()->json(['message' => 'Producto eliminado exitosamente'], 200);
    }
}
