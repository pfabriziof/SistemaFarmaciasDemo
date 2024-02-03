<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProveedorCotizacionDetalle extends Model
{
    protected $table= 'prv_cotizacion_detalle';
    protected $primaryKey= 'id_cotz_detalle_prv';
    protected $fillable = [
        'id_cotizacion_prv',
        'id_producto',
        'nombre_producto',
        'cantidad',
        'id_unidad_medida',
        'und_simbolo',
    ];
    public $timestamps = false;

    protected $with = array('cotizacion_prv', 'producto', 'unidad_medida');

    public function cotizacion_prv(){
        return $this->belongsTo(ProveedorCotizacion::class, 'id_cotizacion_prv');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    public function unidad_medida(){
        return $this->belongsTo(UnidadMedida::class, 'id_unidad_medida');
    }
}
