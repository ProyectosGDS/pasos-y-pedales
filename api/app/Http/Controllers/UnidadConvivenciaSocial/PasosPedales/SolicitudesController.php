<?php

namespace App\Http\Controllers\UnidadConvivenciaSocial\PasosPedales;

use App\Http\Controllers\Controller;
use App\Models\UnidadConvivenciaSocial\PasosPedales\Expediente;
use App\Models\UnidadConvivenciaSocial\PasosPedales\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SolicitudesController extends Controller
{

    public function index() {
        try {
            $solicitudes = Expediente::whereHas('latestWorkflow',function ($query){
                $query->whereIn('estado_id',[1,2]);
            })
            ->with([
                'solicitud.documentos',
                'solicitud.sede',
                'solicitud.tipo_persona',
                'latestWorkflow.estado',
            ])->get();

            return response([
                'message' => 'Se obtuvieron todos las solicitudes existentes',
                'solicitudes' => $solicitudes,
            ]);
            
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error en la obtencion de las solicitudes',
                'error' => 'Error:' + $th->getMessage()
            ]);
        }
    }

    public function store(Request $request) {
        $request->validate([
            'primer_nombre' => 'required|string|max:50',
            'segundo_nombre' => 'required|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'required|string|max:50',
            'cui' => 'required|numeric|digits:13',
            'nit' => 'required|numeric',
            'patente_comercio' => 'required|string|max:50',
            'telefono' => 'required|numeric|digits:8',
            'correo' => 'required|email',
            'zona_id' => 'required|integer|exists:zonas,id',
            'colonia' => 'required|string|max:255',
            'domicilio' => 'required|string|max:255',
            'actividad_negocio' => 'required|string',
            'largo' => 'required|numeric',
            'ancho' => 'required|numeric',
            'observaciones' => 'nullable|string',
            'sede_id' => 'required|integer|exists:sedes,id',
            'tipo_persona_id' => 'required|exists:tipo_persona,id',
            'file_carta_solicitud' => 'required|file|mimes:pdf|max:5120',
            'file_dpi' => 'required|file|mimes:pdf|max:5120',
            'file_rtu' => 'required|file|mimes:pdf|max:5120',
            'file_recibo_servicios' => 'required|file|mimes:pdf|max:5120',
            'file_patente_comercio' => 'required|file|mimes:pdf|max:5120',
            'file_acta_notarial' => [Rule::requiredIf(fn() => $request->tipo_persona_id == 2),'file','mimes:pdf','max:5120']
        ]);

        try {

            $solicitud = Solicitud::create([
                'primer_nombre' => $request->primer_nombre,
                'segundo_nombre' => $request->segundo_nombre,
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'cui' => $request->cui,
                'nit' => $request->nit,
                'patente_comercio' => $request->patente_comercio,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'zona_id' => $request->zona_id,
                'colonia' => $request->colonia,
                'domicilio' => $request->domicilio,
                'actividad_negocio' => $request->actividad_negocio,
                'largo' => $request->largo,
                'ancho' => $request->ancho,
                'observaciones' => $request->observaciones ?? null,
                'sede_id' => $request->sede_id,
                'tipo_persona_id' => $request->tipo_persona_id,
            ]);

            if($solicitud) {
                $solicitud->documentos()->create([
                    'nombre' => 'Carta de solicitud',
                    'path' => $request->file('file_carta_solicitud')->store('UnidadConvivenciaSocial/PasosPedales/Solicitudes'),
                ]);

                $solicitud->documentos()->create([
                    'nombre' => 'Dpi',
                    'path' => $request->file('file_dpi')->store('UnidadConvivenciaSocial/PasosPedales/Solicitudes'),
                ]);

                $solicitud->documentos()->create([
                    'nombre' => 'Rtu',
                    'path' => $request->file('file_rtu')->store('UnidadConvivenciaSocial/PasosPedales/Solicitudes'),
                ]);

                $solicitud->documentos()->create([
                    'nombre' => 'Recibo de servicios',
                    'path' => $request->file('file_recibo_servicios')->store('UnidadConvivenciaSocial/PasosPedales/Solicitudes'),
                ]);

                $solicitud->documentos()->create([
                    'nombre' => 'Patente de comercio',
                    'path' => $request->file('file_patente_comercio')->store('UnidadConvivenciaSocial/PasosPedales/Solicitudes'),
                ]);

                if($request->tipo_persona_id == 2){
                    $solicitud->documentos()->create([
                        'nombre' => 'Acta notarial',
                        'path' => $request->file('file_acta_notarial')->store('UnidadConvivenciaSocial/PasosPedales/Solicitudes'),
                    ]);
                }

                $expediente = Expediente::create([
                    'solicitud_id' => $solicitud->id
                ]);

                if($expediente) {
                    $expediente->workflows()->create([
                        'estado_id' => 1
                    ]);
                }
            }

            return response([
                'solicitud' => $solicitud,
                'message' => 'Se creó la solicitud exitosamente.'
            ]);

        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error en la creacion de la solicitud.'
            ],500);
        }
    }

    public function lineaTiempo(Request $request) {
        try {

            $solicitudes = Expediente::whereHas('latestWorkflow')
            ->with([
                'solicitud.sede',
                'solicitud.tipo_persona',
                'workflows.estado',
                'workflows.user.information',
                'sede'
            ])->advancedFilter($request)->paginate($request->per_page ?? 10);

            if($solicitudes->isEmpty() && $request->searching['search']) {
                $fallbackQuery = Expediente::whereHas('solicitud',function($query) use($request) {
                    $query->whereNombreCompleto($request->searching['search']);
                })->with([
                    'solicitud.sede',
                    'solicitud.tipo_persona',
                    'workflows.estado',
                    'workflows.user.information',
                    'sede' 
                ]);

                $solicitudes = $fallbackQuery->paginate($per_page ?? 10);
            }

            return response($solicitudes);
            
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error en la obtencion de las solicitudes',
                'error' => 'Error:' + $th->getMessage()
            ]);
        }
    }

    public function revisarSolicitud(Expediente $expediente) {

        try {

            if($expediente->workflows()->where('estado_id',2)->first()) {
                return response([
                    'message' => 'El expediente ya esta en revision'
                ]);
            }

            $expediente->workflows()->create([
                'user_id' => Auth::user()->id,
                'estado_id' => 2,
            ]);

            return response([
                'expediente' => $expediente,
                'message' => 'Revisando solicitud'
            ]);
        } catch (\Throwable $th) {
            return response([
                'Error' => $th->getMessage(),
                'Message' => 'Error al intentar cambiar de estado'
            ],500);
        }
    }

    public function aceptarSolicitud(Request $request, Expediente $expediente) {

        try {

            $expediente->workflows()->create([
                'observacion' => $request->observacion,
                'user_id' => Auth::user()->id,
                'estado_id' => 3,
            ]);

            return response([
                'expediente' => $expediente->load([]),
                'message' => 'Solicitud aceptada exitosamente'
            ]);
        } catch (\Throwable $th) {
            return response([
                'Error' => $th->getMessage(),
                'Message' => 'Error al intentar cambiar de estado'
            ],500);
        }
    }

    public function rechazarSolicitud(Request $request, Expediente $expediente) {
        $request->validate([
            'observacion' => 'required|string'
        ]);

        try {
            $expediente->workflows()->create([
                'observacion' => $request->observacion,
                'user_id' => Auth::user()->id,
                'estado_id' => 7,
            ]);

            return response([
                'expediente' => $expediente->load([]),
                'message' => 'Solicitud rechazada exitosamente'
            ]);
        } catch (\Throwable $th) {
            return response([
                'Error' => $th->getMessage(),
                'Message' => 'Error al intentar cambiar de estado'
            ],500);
        }
    }
}
