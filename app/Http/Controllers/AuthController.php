<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\CustomMethodNotAllowedHttpException;

class AuthController extends Controller {
    public function register(Request $request) {
        // Validación de los datos del usuario prueba de cambio
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'admin_password' => 'required|string'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            // Check if the email field has a unique constraint error
            if ($errors->has('email')) {
                return response()->json(['message' => 'El correo electrónico ya está registrado'], 400);
            }
            return response()->json($errors, 400);
        }

        // Verificación de la contraseña del administrador
        $adminPassword = env('ADMIN_PASSWORD', 'default_password');
        if ($request->admin_password !== $adminPassword) {
            return response()->json(['message' => 'Contraseña de administrador incorrecta'], 401);
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
    public function index() {
        $users = User::latest()->get();
        return response()->json($users);
    }


    public function searchUsers(Request $request) {
        // Obtener el parámetro de búsqueda desde la solicitud
        $search = $request->input('search');

        // Si el parámetro de búsqueda está presente, filtrar los usuarios
        if ($search) {
            $users = User::where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });

            if ($search === 'Administrador Dueño') {
                $users->orWhere('role', 0);
            } elseif ($search === 'Administrador Trabajador') {
                $users->orWhere('role', 1);
            } elseif ($search === 'Trabajador') {
                $users->orWhere('role', 2);
            }

            $users = $users->get();
        } else {
            // Si no hay parámetro de búsqueda, obtener todos los usuarios
            $users = User::latest()->get();
        }

        return response()->json($users);
    }


    public function login(Request $request) {

        if (!$request->isMethod('post')) {
            throw new CustomMethodNotAllowedHttpException();
        }

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
                'email' => $user->email,
                'role' => $user->role


            ]
        ]);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Sesión terminada']);
    }

    public function show($id) {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($user);
    }

  
    public function update(Request $request, $id) {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
    
        // Validación de los datos de la solicitud
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'email|max:255|unique:users,email,' . $id,
            'password' => 'string|min:8|nullable', // 'nullable' permite que el campo sea opcional
            'role' => 'string',
            'admin_password' => 'required|string'
        ]);
    
        if ($validator->fails()) {
            $errors = $validator->errors();
            // Check if the email field has a unique constraint error
            if ($errors->has('email')) {
                return response()->json(['message' => 'El correo electrónico ya está registrado'], 400);
            }
            return response()->json($errors, 400);
        }
    
        // Verificación de la contraseña del administrador
        $adminPassword = env('ADMIN_PASSWORD', 'default_password');
        if ($request->admin_password !== $adminPassword) {
            return response()->json(['message' => 'Contraseña de administrador incorrecta'], 401);
        }
    
        // Si se proporciona una nueva contraseña, encriptarla antes de actualizar
        if ($request->filled('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        } else {
            // Si no se proporciona una nueva contraseña, eliminar el campo para no actualizarlo
            $request->request->remove('password');
        }
    
        // Actualizar el usuario con los datos validados
        $user->update($request->except(['admin_password']));
    
        return response()->json(['message' => 'Usuario actualizado exitosamente', 'user' => $user]);
    }
    
    public function destroy(Request $request, $id) {
        // Validar que se proporcione la contraseña del administrador
        $request->validate([
            'admin_password' => 'required|string',
        ]);

        // Verificación de la contraseña del administrador
        $adminPassword = env('ADMIN_PASSWORD', 'default_password');
        if ($request->admin_password !== $adminPassword) {
            return response()->json(['message' => 'Contraseña de administrador incorrecta'], 401);
        }

        // Buscar el usuario por ID
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Eliminar el usuario
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado con éxito']);
    }
}
