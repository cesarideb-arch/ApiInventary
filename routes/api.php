<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EntranceController;
use App\Http\Controllers\OutputController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('register', [AuthController::class, 'register']);

    // Rutas de usuarios
    Route::get('/users', [AuthController::class, 'index']);
    Route::get('/searchUsers', [AuthController::class, 'searchUsers']);
    Route::get('/users/{id}', [AuthController::class, 'show']);
    Route::put('/users/{id}', [AuthController::class, 'update']);
    Route::delete('/users/{id}', [AuthController::class, 'destroy']);


    // Rutas de productos
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::get('/getCountProducts', [ProductController::class, 'getCountProducts']);
    Route::get('/getprojects', [ProductController::class, 'getprojects']);
    // Rutas de personalización    
    Route::get('/getCategoryProducts', [ProductController::class, 'getCategoryProducts']);
    Route::get('/search', [ProductController::class, 'SearchGet']);

    // Rutas de proveedores
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);
    Route::get('/searchSupplier', [SupplierController::class, 'SearchSupplier']);

    // Rutas de proyectos
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{id}', [ProjectController::class, 'show']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    Route::get('/searchProject', [ProjectController::class, 'SearchProject']);

    // Rutas de categorías
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    Route::get('/searchCategory', [CategoryController::class, 'SearchCategory']);

    // Rutas de entradas
    Route::post('/entrances', [EntranceController::class, 'store']);
    Route::get('/entrances', [EntranceController::class, 'index']);
    Route::get('/entrances/{id}', [EntranceController::class, 'show']);
    Route::put('/entrances/{id}', [EntranceController::class, 'update']);
    Route::delete('/entrances/{id}', [EntranceController::class, 'destroy']);
    Route::get('/searchEntrance', [EntranceController::class, 'SearchEntrance']);
    Route::get('/GetProductEntrance', [EntranceController::class, 'GetProductEntrance']);
    Route::get('/GetEntrancesCount', [EntranceController::class, 'GetEntrancesCount']);

    // Rutas de salidas
    Route::post('/outputs', [OutputController::class, 'store']);
    Route::get('/outputs', [OutputController::class, 'index']);
    Route::get('/outputs/{id}', [OutputController::class, 'show']);
    Route::put('/outputs/{id}', [OutputController::class, 'update']);
    Route::delete('/outputs/{id}', [OutputController::class, 'destroy']);
    Route::get('/searchOutput', [OutputController::class, 'SearchOutput']);
    Route::get('/GetProductOutput', [OutputController::class, 'GetProductOutput']);
    Route::get('/GetOutputsCount', [OutputController::class, 'GetOutputsCount']);

    // Rutas de préstamos
    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/loans', [LoanController::class, 'index']);
    Route::get('/loans/{id}', [LoanController::class, 'show']);
    Route::put('/loans/{id}', [LoanController::class, 'update']);
    Route::delete('/loans/{id}', [LoanController::class, 'destroy']);
    Route::put('/comeBackLoan/{id}', [LoanController::class, 'comeBackLoan']);
    Route::get('/getCount', [LoanController::class, 'getCount']);
    Route::get('/searchLoan', [LoanController::class, 'SearchLoan']);
    Route::get('/GetProductLoan', [LoanController::class, 'GetProductLoan']);
});
