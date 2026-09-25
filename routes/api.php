<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

 Route::get('/test',function(){
    return response()->json([
        'message'=>'API laravel berhasil'
    ]);
 });
 Route::post('/login',[AuthController::class, 'login']);
 Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout',[AuthController::class, 'logout']);
     Route::apiResource('products',ProductController::class);

 });
