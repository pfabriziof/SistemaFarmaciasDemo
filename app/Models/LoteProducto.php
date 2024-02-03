<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteProducto extends Model
{
    protected $table= 'lote_productos';
    protected $primaryKey= 'id_lote';
    protected $fillable = [
        'lote',
        'cantidad',
        'fecha_expiracion',
        'id_producto',
        'id_sucursal'
    ];
    
    public $timestamps = false;

}