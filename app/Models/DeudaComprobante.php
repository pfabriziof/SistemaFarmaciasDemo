<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeudaComprobante extends Model
{
    protected $table= 'deudas_comprobantes';
    protected $primaryKey= 'id_deuda';
    protected $fillable = [
        'id_comprobante',
        'id_cliente', 
        'total_adelanto', 
        'total_deuda', 
        'total_monto_pagado', 
        'total_monto_pendiente', 
        'fecha',
        'estado',
    ];
    public $timestamps = false;

    protected $with = array('comprobante', 'cliente');

    public function comprobante(){
        return $this->belongsTo(Comprobante::class, 'id_comprobante');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
}
