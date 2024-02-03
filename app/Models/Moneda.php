<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    protected $table= 'monedas';
    protected $primaryKey= 'id_moneda';
    protected $fillable = [
        'moneda', 'nombre', 'simbolo',
    ];
    public $timestamps = false;
}
