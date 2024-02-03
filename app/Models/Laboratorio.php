<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratorio extends Model
{
    protected $table= 'laboratorios';
    protected $primaryKey= 'id_laboratorio';
    protected $fillable = [
        'nombre',
        'estado',
    ];
    public $timestamps = false;
}
