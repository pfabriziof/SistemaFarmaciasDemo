<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEgreso extends Model
{
    protected $table= 'tipos_egreso';
    protected $primaryKey= 'id_tipo_egreso';
    protected $fillable = [
        'tipo_egreso'
    ];
    public $timestamps = false;
}
