<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeudaCompra extends Model
{
    protected $table= 'deudas_compras';
    protected $primaryKey= 'id_deuda';
    protected $fillable = [
        'id_compra', 
        'id_proveedor',
        'total_adelanto', 
        'total_deuda', 
        'total_monto_pagado', 
        'total_monto_pendiente', 
        'fecha',
        'estado',
    ];
    public $timestamps = false;

    protected $with = array('compra', 'proveedor');

    public function compra(){
        return $this->belongsTo(Compra::class, 'id_compra');
    }

    public function proveedor(){
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }
}
