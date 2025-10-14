<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;

class ZonasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $zonas=Zona::all();
            return response([
                'zonas' => $zonas,
                'message' => 'Se obtuvieron todas las zonas exitosamente.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error al obtener las zonas.'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $request->validate([
            'nombre' => 'required|string|max:255'
        ]);
        try {
            $zona=Zona::create([
                'nombre' => $request->nombre
            ]);
            return response([
                'zona' => $zona,
                'message' => 'Se creo la zona exitosamente.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error al crear la zona.'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zona $zona) {
        $request->validate([
            'nombre' => 'required|string|max:255'
        ]);

        try {
            $zona->nombre = $request->nombre;
            $zona->save();

            return response([
                'zona' => $zona,
                'message' => 'Se actualizo la zona exitosamente.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error al actualizar la zona.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Zona $zona) {
        try {
            $zona->delete();

            return response([
                'zona' => $zona,
                'message' => 'Se elimino la zona exitosamente.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error al elminar la zona.'
            ]);
        }
    }
}
