<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteDireccion extends Model
{
    protected $table= 'cliente_direcciones';
    protected $primaryKey= 'id_direccion';
    protected $fillable = [
        'id_cliente',
        'direccion',
        'estado',
    ];
    public $timestamps = false;
}
