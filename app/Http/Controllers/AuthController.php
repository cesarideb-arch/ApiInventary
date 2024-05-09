<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
{
    // Validación de los datos del usuario
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role' => 'required|string'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    // Creación del usuario
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password), // Asegúrate de usar Hash para encriptar la contraseña
        'role' => $request->role
    ]);

    // Generación del token
    $token = $user->createToken('PersonalAccess')->plainTextToken;

    return response()->json([
        'message' => 'Usuario registrado con éxito',
        'user' => $user,
        'token' => $token
    ], 201);
}

      

    



    public function login(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if ($validateData->fails()) {
            throw ValidationException::withMessages([
                'message' => 'Los siguientes campos son requeridos',
                'errors' => $validateData->errors()
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        return response()->json([
            'token' => $user->createToken('AuthToken')->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }   

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Sesión terminada']);
    }
}
