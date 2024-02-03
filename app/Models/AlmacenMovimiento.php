<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlmacenMovimiento extends Model
{
    protected $table= 'almacen_movimientos';
    protected $primaryKey= 'id_almacen_movimientos';
    protected $fillable = [
        'id_sucursal',
        'id_usuario',
        'id_tipo_movimiento',
        'id_producto',
        'NombreProducto',
        'id_unidad_medida',
        'und_simbolo',
        'cantidad',
        'precioUnitario',
        'precioTotal',
        'stock_actual',
        'fecha_movimiento',
    ];
    public $timestamps = true;

    protected $with = array('usuario', 'sucursal', 'tipo_movimiento', 'producto', 'unidad_medida');

    public function usuario(){
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
    public function tipo_movimiento(){
        return $this->belongsTo(TipoMovimiento::class, 'id_tipo_movimiento');
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    public function unidad_medida(){
        return $this->belongsTo(UnidadMedida::class, 'id_unidad_medida');
    }
}
