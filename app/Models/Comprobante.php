<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    protected $table= 'comprobantes';
    protected $primaryKey= 'id_comprobante';
    protected $guarded = [
        'id_usuario',
        'id_sucursal',
    ];
    protected $fillable = [
        'id_tipo_comprobante',
        'id_estado_comprobante',
        'id_cliente',
        'id_moneda',
        'id_medio_pago',
        'id_tipo_cambio',
        'id_serie',

        'nombreCliente',
        'nroDocCliente',
        'direccionCliente',
        'fecha_emision',
        'fecha_vencimiento',
        'comentario',

        'op_inafectas',
        'op_exoneradas',
        'op_gravadas',
        'icbper',
        'porcentaje_igv',
        'igv',
        'total',

        'correlativo',
        'external_id',
        'formato_impresion',
        'fecha_anulacion',
        'motivo_anulacion',

        'time_elapsed',
    ];
    public $timestamps = true;

    protected $with = array('cliente', 'tipo_comprobante', 'usuario', 'sucursal', 'moneda', 'estado', 'medio_pago', 'serie', 'tipo_cambio');

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
    public function tipo_comprobante(){
        return $this->belongsTo(TipoComprobante::class, 'id_tipo_comprobante');
    }
    public function usuario(){
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
    public function moneda(){
        return $this->belongsTo(Moneda::class, 'id_moneda');
    }
    public function estado(){
        return $this->belongsTo(ComprobanteEstado::class, 'id_estado_comprobante');
    }
    public function medio_pago(){
        return $this->belongsTo(MedioPago::class, 'id_medio_pago');
    }
    public function serie(){
        return $this->belongsTo(SerieInv::class, 'id_serie');
    }
    public function tipo_cambio(){
        return $this->belongsTo(TipoCambio::class, 'id_tipo_cambio');
    }
}