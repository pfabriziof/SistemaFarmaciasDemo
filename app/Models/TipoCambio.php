<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCambio extends Model
{
    protected $table= 'tipo_cambio';
    protected $primaryKey= 'id_tipo_cambio';
    protected $fillable = [
        'tipo_cambio',
        'cambio',
    ];
    public $timestamps = false;
}
