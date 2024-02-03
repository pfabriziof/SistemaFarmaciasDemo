<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCompraDetalle extends Model
{
    protected $table= 'orden_compra_detalle';
    protected $primaryKey= 'id_orden_detalle';
    protected $fillable = [
        'id_orden_compra',
        'id_producto',
        'nombre_producto',
        'id_unidad_medida',
        'und_simbolo',
        'id_lista_detalle',
        
        'cantidad',
        'cantidad_visual',
        'precio_unitario',
        'precio_total'
    ];
    public $timestamps = false;

    protected $with = array('orden_compra', 'producto', 'unidad_medida', 'lista_detalle');

    public function orden_compra(){
        return $this->belongsTo(OrdenCompra::class, 'id_orden_compra');
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
}
