<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    protected $table= 'tipos_movimiento';
    protected $primaryKey= 'id_tipo_movimiento';
    protected $fillable = [
        'tipo_movimiento'
    ];
    public $timestamps = false;
}
