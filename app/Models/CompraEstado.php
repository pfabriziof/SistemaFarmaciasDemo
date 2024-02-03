<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraEstado extends Model
{
    protected $table= 'compra_estado';
    protected $primaryKey= 'id';
    protected $fillable = [
        'estado',
    ];
    public $timestamps = false;
}