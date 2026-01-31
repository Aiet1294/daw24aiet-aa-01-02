<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TranslatorController;

// Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', [TranslatorController::class, 'index']);
Route::post('/', [TranslatorController::class, 'translate']);