<?php

namespace App\Models\UnidadConvivenciaSocial\PasosPedales;

use Illuminate\Database\Eloquent\Model;

class TipoPersona extends Model
{
    protected $table = 'tipo_persona';
    protected $fillable = ['nombre'];
    public $timestamps = false;

    public function solicitudes() {
        return $this->hasMany(Solicitud::class);
    }
}
