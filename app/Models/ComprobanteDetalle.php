<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobanteDetalle extends Model
{
    protected $table= 'comprobante_detalle';
    protected $primaryKey= 'id_comp_detalle';
    protected $fillable = [
        'id_comprobante',
        'id_producto',
        'nombreProducto',
        'id_unidad_medida',
        'und_simbolo',
        'id_lista_detalle',

        'id_lote',
        'lote_producto',

        'cantidad',
        'cantidad_visual',
        'precio_unitario',
        'precio_total',
    ];
    public $timestamps = false;

    protected $with = array('comprobante', 'producto', 'unidad_medida', 'lote', 'lista_detalle');

    public function comprobante(){
        return $this->belongsTo(Comprobante::class, 'id_comprobante');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    public function unidad_medida(){
        return $this->belongsTo(UnidadMedida::class, 'id_unidad_medida');
    }
    public function lote(){
        return $this->belongsTo(LoteProducto::class, 'id_lote');
    }
    public function lista_detalle(){
        return $this->belongsTo(ListaPreciosDetalle::class, 'id_lista_detalle');
    }
}
