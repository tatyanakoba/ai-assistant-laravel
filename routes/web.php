<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/personasAI', [App\Http\Controllers\PersonaController::class, 'show']);
Route::post('persona', [App\Http\Controllers\PersonaController::class, 'handlePersona']);
Route::post('generate', [App\Http\Controllers\PersonaController::class, 'generateAction']);
