<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeudaComprobantePago extends Model
{
    protected $table= 'deudas_comprobantes_pagos';
    protected $primaryKey= 'id_pago';
    protected $fillable = [
        'id_deuda', 
        'monto_pagado', 
        'comentario', 
        'fecha',
        'estado',
    ];
    public $timestamps = false;

    protected $with = array('deuda');

    public function deuda(){
        return $this->belongsTo(DeudaComprobante::class, 'id_deuda');
    }
}
