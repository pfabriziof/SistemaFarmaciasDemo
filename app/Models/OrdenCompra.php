<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    protected $table= 'orden_compra';
    protected $primaryKey= 'id_orden_compra';
    protected $fillable = [
        'id_proveedor',
        'id_usuario',
        'id_sucursal',
        'id_moneda', 
        'id_medio_pago',
        'id_tipo_cambio',

        'email',
        'fecha_emision',
        'fecha_vencimiento',
        'numeracion',

        'op_inafectas',
        'op_exoneradas',
        'op_gravadas',
        'icbper',
        'porcentaje_igv',
        'igv',
        'total', 

        'estado',
    ];
    public $timestamps = true;

    protected $with = array('proveedor', 'usuario', 'sucursal', 'moneda', 'medio_pago', 'tipo_cambio');

    public function usuario(){
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
    public function proveedor(){
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }
    public function moneda(){
        return $this->belongsTo(Moneda::class, 'id_moneda');
    }
    public function medio_pago(){
        return $this->belongsTo(MedioPago::class, 'id_medio_pago');
    }
    public function tipo_cambio(){
        return $this->belongsTo(TipoCambio::class, 'id_tipo_cambio');
    }
}
