<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaPreciosDetalle extends Model
{
    protected $table = 'lista_precios_detalle';
    protected $primaryKey = 'id_lista_detalle';
    protected $fillable = [
        'id_producto',
        'id_sucursal',
        'id_lista_precio',
        'precio_compra',
        'precio_venta',
        'unidades',
        'estado',
    ];
    
    public $timestamps = false;

    protected $with = array('listaprecio', 'producto');

    public function listaprecio(){
        return $this->belongsTo(ListaPrecios::class,'id_lista_precio');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}