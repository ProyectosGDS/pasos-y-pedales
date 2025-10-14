<?php

namespace App\Http\Controllers\UnidadConvivenciaSocial\PasosPedales;

use App\Http\Controllers\Controller;
use App\Models\UnidadConvivenciaSocial\PasosPedales\TipoPersona;
use Illuminate\Http\Request;

class TipoPersonasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $tipo_personas = TipoPersona::all();
            return response([
                'tipo_personas' => $tipo_personas,
                'message' => 'Se obtuvo los tipos de personas existentes.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error en la obtencion de las solicitudes'
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
            $tipo_persona = TipoPersona::create([
                'nombre' => $request->nombre
            ]);
            return response([
                'tipo_persona' => $tipo_persona,
                'message' => 'Tipo persona creada exitosamente.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error al crear un tipo de persona.'
            ]);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoPersona $tipo_persona) {

        $request->validate([
            'nombre' => 'required|string|max:255'
        ]);

        try {
            $tipo_persona->nombre = $request->nombre;
            $tipo_persona->save();

            return response([
                'tipo_persona' => $tipo_persona,
                'message' => 'Tipo persona actualizada exitosamente.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error al actualizar el tipo de persona.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoPersona $tipo_persona) {
        try {
            $tipo_persona->delete();

            return response([
                'tipo_persona' => $tipo_persona,
                'message' => 'Tipo persona eliminada exitosamente.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error al eliminar el tipo persona.'
            ]);
        }
    }
}
