<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos_servicios';
    protected $primaryKey = 'id_producto';
    protected $fillable = [
        'id_categoria',
        'id_marca',
        'id_unidad_medida',
        'id_sucursal',
        'id_laboratorio',
        'id_condicion_alm',
        'id_tipo_producto',
        'codigo_producto',
        'nombreProducto',
        'servicio',
        'stock',
        'stock_minimo',
        'principio_activo',
        'indicaciones',
        'concentracion',
        'registro_sanitario',
        'vigencia_registro',
        'ubicacion',
        'estado'
    ];
    
    public $timestamps = true;
    protected $with = array('sucursal', 'categoria', 'marca', 'unidad_medida', 'laboratorio', 'condicion_almacenamiento', 'tipo_producto', 'lotes');

    public function categoria(){
        return $this->belongsTo(ProductoCategoria::class,'id_categoria');
    }
    public function marca(){
        return $this->belongsTo(Marca::class,'id_marca');
    }
    public function unidad_medida(){
        return $this->belongsTo(UnidadMedida::class,'id_unidad_medida');
    }
    public function laboratorio(){
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
    }
    public function condicion_almacenamiento(){
        return $this->belongsTo(CondicionAlmacenamiento::class,'id_condicion_alm');
    }
    public function tipo_producto(){
        return $this->belongsTo(ProductoTipo::class,'id_tipo_producto');
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
    public function lotes(){
        return $this->hasMany(LoteProducto::class, 'id_producto');
    }
    // public function pricelist_detail(){
    //     return $this->hasMany(ListaPreciosDetalle::class, 'id_producto');
    // }
}
