<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table= 'compras';
    protected $primaryKey= 'id_compra';

    protected $guarded = [
        'id_usuario',
        'id_sucursal',
    ];
    protected $fillable = [
        'id_proveedor',
        'id_moneda',
        'id_medio_pago',
        'id_tipo_cambio',
        'id_tipo_comprobante',
        'correlativo',
        'nombreProveedor',
        'nroDocProveedor',
        'email',
        'nro_guia_remision',
        'serie_factura',
        'nro_factura',
        'fecha_emision',
        'fecha_vencimiento',
        'origen_dinero',

        'op_inafectas',
        'op_exoneradas',
        'op_gravadas',
        'icbper',
        'porcentaje_igv',
        'igv',
        'total',

        'deuda_id',
        'deuda_generada',
        'deuda_adelanto',

        'id_estado',
        'fecha_anulacion',

        'time_elapsed',
    ];
    public $timestamps = true;

    protected $with = array('usuario', 'sucursal', 'proveedor', 'moneda', 'medio_pago', 'tipo_cambio', 'tipo_comprobante', 'estado');

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
    public function tipo_comprobante(){
        return $this->belongsTo(TipoComprobante::class, 'id_tipo_comprobante');
    }
    public function estado(){
        return $this->belongsTo(CompraEstado::class, 'id_estado');
    }
}
