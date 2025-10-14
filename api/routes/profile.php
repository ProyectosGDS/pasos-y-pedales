<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('profile/{user}',[ProfileController::class,'show']);
Route::put('profile/{user}',[ProfileController::class,'update']);
Route::put('profile/change-password/{user}',[ProfileController::class,'changePassword']);
Route::post('profile/upload-picture/{user}',[ProfileController::class,'uploadPicture']);
Route::delete('profile/delete-picture/{user}',[ProfileController::class,'deletePicture']);
