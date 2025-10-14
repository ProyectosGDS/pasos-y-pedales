<?php

use App\Http\Controllers\UnidadConvivenciaSocial\PasosPedales\AsignacionesController;
use App\Http\Controllers\UnidadConvivenciaSocial\PasosPedales\AutorizacionesController;
use App\Http\Controllers\UnidadConvivenciaSocial\PasosPedales\SedesController;
use App\Http\Controllers\UnidadConvivenciaSocial\PasosPedales\SolicitudesController;
use App\Http\Controllers\UnidadConvivenciaSocial\PasosPedales\TipoPersonasController;
use Illuminate\Support\Facades\Route;

Route::apiResource('sedes',SedesController::class)->except(['show']);
Route::apiResource('tipo-personas',TipoPersonasController::class)->except(['show']);

Route::prefix('solicitudes')->group(function () {
    Route::post('/',[SolicitudesController::class,'store']);
    Route::get('ingresadas',[SolicitudesController::class,'index']);
    Route::get('linea-de-tiempo',[SolicitudesController::class,'lineaTiempo']);
    Route::put('revisar-solicitud/{expediente}',[SolicitudesController::class,'revisarSolicitud']);
    Route::put('aceptar-solicitud/{expediente}',[SolicitudesController::class,'aceptarSolicitud']);

    Route::put('rechazar-solicitud/{expediente}',[SolicitudesController::class,'rechazarSolicitud']);

    Route::get('aceptadas',[AsignacionesController::class,'index']);
    Route::put('verificar-espacio/{expediente}',[AsignacionesController::class,'verificarEspacio']);
    Route::put('asignar-espacio/{expediente}',[AsignacionesController::class,'asignarEspacio']);

    Route::get('asignadas',[AutorizacionesController::class,'index']);
    Route::put('rechazar-asignacion/{expediente}',[AutorizacionesController::class,'rechazarAsignacion']);
    Route::put('autorizar-solicitud/{expediente}',[AutorizacionesController::class,'autorizarSolicitud']);

});
