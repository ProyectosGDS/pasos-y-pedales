<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/csrf-cookie', fn(Request $request) => response(''));
Route::get('/verify-auth', [LoginController::class, 'verifyAuth'])->middleware(['jwt-auth']);

Route::post('/login', [LoginController::class, 'authenticate']);




