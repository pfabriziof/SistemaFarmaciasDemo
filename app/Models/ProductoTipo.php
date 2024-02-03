<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoTipo extends Model
{
    protected $table = 'producto_tipos';
    protected $primaryKey = 'id_producto_tipo';
    protected $fillable = [
        'tipo', 'impuesto', 'icbper',
    ];
    
    public $timestamps = false;
}
