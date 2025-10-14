<?php

namespace App\Http\Controllers\UnidadConvivenciaSocial\PasosPedales;

use App\Http\Controllers\Controller;
use App\Models\UnidadConvivenciaSocial\PasosPedales\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsignacionesController extends Controller
{
    public function index() {
        try {
            $expedientes = Expediente::whereHas('latestWorkflow', function($query){
                $query->whereIn('estado_id',[3,4,8]);
            })
            ->with([
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

    public function verificarEspacio(Expediente $expediente) {

        try {

            if($expediente->workflows()->where('estado_id',4)->first()) {
                return response([
                    'message' => 'El expediente ya esta en verificación de espacio'
                ]);
            }

            $expediente->workflows()->create([
                'user_id' => Auth::user()->id,
                'estado_id' => 4,
            ]);

            return response([
                'expediente' => $expediente,
                'message' => 'Verificando espacio'
            ]);
        } catch (\Throwable $th) {
            return response([
                'Error' => $th->getMessage(),
                'Message' => 'Error al intentar cambiar de estado'
            ]);
        }
    }

    public function asignarEspacio(Request $request, Expediente $expediente) {

        $request->validate([
            'largo' => 'required|decimal:2',
            'ancho' => 'required|decimal:2',
            'sede_id' => 'required|exists:sedes,id',
            'descripcion' => 'nullable|string',
        ]);

        try {

            $expediente->ancho = $request->ancho;
            $expediente->largo = $request->largo;
            $expediente->sede_id = $request->sede_id;
            $expediente->descripcion = $request->descripcion ?? null;
            $expediente->save();


            $expediente->workflows()->create([
                'user_id' => Auth::user()->id,
                'estado_id' => 5,
            ]);

            return response([
                'expediente' => $expediente,
                'message' => 'Espacio asignado exitosamente'
            ]);

        } catch (\Throwable $th) {
            return response([
                'Error' => $th->getMessage(),
                'Message' => 'Error al intentar cambiar de estado'
            ]);
        }
    }
        
}
