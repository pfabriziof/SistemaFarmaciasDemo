<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EgresoMotivo extends Model
{
    protected $table= 'egreso_motivos';
    protected $primaryKey= 'id_egreso_motivo';
    protected $fillable = [
        'motivo'
    ];
    public $timestamps = false;
}
