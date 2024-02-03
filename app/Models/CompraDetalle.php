<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    protected $table= 'compras_detalle';
    protected $primaryKey= 'id_compra_detalle';
    protected $fillable = [
        'id_compra',
        'id_producto',
        'nombre_producto',
        'id_unidad_medida',
        'und_simbolo',
        'id_lista_detalle',

        'lote_id',
        'lote_name',
        'lote_fecha_exp',

        'cantidad',
        'cantidad_visual',
        'precio_unitario',
        'precio_total',
    ];
    public $timestamps = false;

    protected $with = array('compra', 'producto', 'unidad_medida', 'lista_detalle', 'lote');

    public function compra(){
        return $this->belongsTo(Compra::class, 'id_compra');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    public function unidad_medida(){
        return $this->belongsTo(UnidadMedida::class, 'id_unidad_medida');
    }
    public function lista_detalle(){
        return $this->belongsTo(ListaPreciosDetalle::class, 'id_lista_detalle');
    }
    public function lote(){
        return $this->belongsTo(LoteProducto::class, 'lote_id');
    }
}
