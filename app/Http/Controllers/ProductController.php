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
        'name' => 'required|string|max:50',
        'model' => 'nullable|string|max:50',
        'unit_measure' => 'nullable|string|max:15',
        'brand' => 'nullable|string|max:50',
        'quantity' => 'required|integer',
        'description' => 'nullable|string',
        'price' => 'required|numeric|between:0,999999.99',
        'profile_image' => 'nullable|file|max:2048|mimes:jpeg,png,gif,svg', // Asegúrate de que el archivo tiene un tamaño máximo de 2MB y es de tipo imagen (jpeg, png, gif, svg)
        'provider' => 'nullable|string|max:50',
        'serie' => 'nullable|string|max:40',
        'observations' => 'nullable|string|max:50',
        'location' => 'nullable|string|max:20',
        'category' => 'nullable|string|max:20',
    ]);

    // Comprobar si la solicitud contiene una imagen
    if ($request->hasFile('profile_image')) {
        $file = $request->file('profile_image');
        $extension = $file->getClientOriginalExtension();
        $new_name = time() . '_1.' . $extension;
        
        // Mover la imagen a la carpeta public/images
        $file->move(public_path('images'), $new_name);
        
        // Ruta completa de la imagen
        $imagePath = 'images/' . $new_name;
        
        // Asignar la ruta de la imagen al campo profile_image
        $validated['profile_image'] = $imagePath;
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
             'name' => 'required|string|max:50',
             'model' => 'nullable|string|max:50',
             'unit_measure' => 'nullable|string|max:15',
             'brand' => 'nullable|string|max:50',
             'quantity' => 'required|integer',
             'description' => 'nullable|string',
             'price' => 'required|numeric|between:0,999999.99',
             'profile_image' => 'nullable|file|max:2048|mimes:jpeg,png,gif,svg', // Asegúrate de que el archivo tiene un tamaño máximo de 2MB y es de tipo imagen (jpeg, png, gif, svg)
             'provider' => 'nullable|string|max:50',
             'serie' => 'nullable|string|max:40',
             'observations' => 'nullable|string|max:50',
             'location' => 'nullable|string|max:20',
             'category' => 'nullable|string|max:20',
         ]);
     
         // Comprobar si la solicitud contiene una imagen
         if ($request->hasFile('profile_image')) {
             if (File::exists(public_path($product->profile_image))) {
                 // Eliminar la imagen anterior si existe
                 File::delete(public_path($product->profile_image));
             }
     
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
