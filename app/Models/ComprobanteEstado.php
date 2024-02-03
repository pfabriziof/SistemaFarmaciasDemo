<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobanteEstado extends Model
{
    protected $table= 'comprobante_estados';
    protected $primaryKey= 'id_estado_comprobante';
    protected $fillable = [
        'estado',
    ];
    public $timestamps = false;
}
