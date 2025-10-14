<?php

namespace App\Http\Controllers\UnidadConvivenciaSocial\PasosPedales;

use App\Http\Controllers\Controller;
use App\Models\UnidadConvivenciaSocial\PasosPedales\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutorizacionesController extends Controller
{
    public function index() {
        try {
            $expedientes = Expediente::whereHas('latestWorkflow', function($query){
                $query->where('estado_id',5);
            })
            ->with([
                'sede',
                'solicitud.documentos',
                'solicitud.sede',
                'solicitud.tipo_persona',
                'latestWorkflow.estado',
            ])->get();

            return response([
                'message' => 'Se obtuvieron todos las expedientes existentes',
                'expedientes' => $expedientes,
            ]);
            
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error en la obtencion de las expedientes',
                'error' => 'Error:' + $th->getMessage()
            ]);
        }
    }

    public function rechazarAsignacion(Request $request, Expediente $expediente) {
        $request->validate([
            'observacion' => 'required|string'
        ]);

        try {

            $expediente->workflows()->create([
                'observacion' => $request->observacion,
                'user_id' => Auth::user()->id,
                'estado_id' => 8,
            ]);

            return response([
                'expediente' => $expediente,
                'message' => 'Se rechaza asignacion exitosamente'
            ]);
        } catch (\Throwable $th) {
            return response([
                'Error' => $th->getMessage(),
                'Message' => 'Error al intentar cambiar de estado'
            ]);
        }
    }

    public function autorizarSolicitud(Request $request, Expediente $expediente) {

        try {

            $expediente->workflows()->create([
                'observacion' => $request->observacion ?? null,
                'user_id' => Auth::user()->id,
                'estado_id' => 6,
            ]);

            return response([
                'expediente' => $expediente,
                'message' => 'Expediente autorizado'
            ]);

        } catch (\Throwable $th) {
            return response([
                'Error' => $th->getMessage(),
                'Message' => 'Error al intentar cambiar de estado'
            ]);
        }
    }
}
