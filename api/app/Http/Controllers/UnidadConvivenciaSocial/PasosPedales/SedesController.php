<?php

namespace App\Http\Controllers\UnidadConvivenciaSocial\PasosPedales;

use App\Http\Controllers\Controller;
use App\Models\UnidadConvivenciaSocial\PasosPedales\Sede;
use Illuminate\Http\Request;

class SedesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {

            $sedes = Sede::all();
            
            return response([
                'sedes' => $sedes,
                'message' => 'Sedes cargadas exitosamente.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error en cargar sedes.'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        try {
            return response([
                'message' => 'Sedes cargadas exitosamente.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error en cargar sedes.'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sede $sede) {
        try {
            return response([
                'message' => 'Sedes cargadas exitosamente.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error en cargar sedes.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sede $sede) {
        try {
            return response([
                'message' => 'Sedes cargadas exitosamente.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error en cargar sedes.'
            ]);
        }
    }
}
