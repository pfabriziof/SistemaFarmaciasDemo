<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table= 'marcas_productos';
    protected $primaryKey= 'id_marca';
    protected $fillable = [
        'marca',
        'estado',
    ];
    public $timestamps = false;
}
