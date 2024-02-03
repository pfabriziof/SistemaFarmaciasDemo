<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $table= 'unidades_medida';
    protected $primaryKey= 'id_unidad_medida';
    protected $fillable = [
        'unidad_medida',
        'codigo_sunat',
        'simbolo',
        'estado'
    ];
    public $timestamps = false;
}
