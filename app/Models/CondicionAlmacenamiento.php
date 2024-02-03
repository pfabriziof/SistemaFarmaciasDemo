<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CondicionAlmacenamiento extends Model
{
    protected $table= 'condiciones_almacenamiento';
    protected $primaryKey= 'id_condicion_alm';
    protected $fillable = [
        'descripcion',
        'estado',
    ];
    public $timestamps = false;
}