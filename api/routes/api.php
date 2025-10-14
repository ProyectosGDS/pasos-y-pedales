<?php

use App\Http\Controllers\ZonasController;
use Illuminate\Support\Facades\Route;

Route::apiResource('zonas',ZonasController::class);