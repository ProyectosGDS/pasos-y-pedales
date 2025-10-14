<?php

namespace App\Models\UnidadConvivenciaSocial\PasosPedales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function expedientes() {
        return $this->hasMany(Expediente::class);
    }

    public function solicitudes() {
        return $this->hasMany(Solicitud::class);
    }
}
