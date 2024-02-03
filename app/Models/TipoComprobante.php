<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoComprobante extends Model
{
    protected $table= 'tipos_comprobante';
    protected $primaryKey= 'id_tipo_comprobante';
    protected $fillable = [
        'tipo_comprobante', 'codigo_sunat',
    ];
    public $timestamps = false;
}
