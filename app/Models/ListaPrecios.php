<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaPrecios extends Model
{
    protected $table = 'lista_precios';
    protected $primaryKey = 'id_lista_precio';
    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
    ];
    
    public $timestamps = false;

    public function listas_detalle(){
        return $this->hasMany(ListaPreciosDetalle::class, 'id_lista_precio');
    }

    public function scopeActive($q)
    {
        //scope users where active 1
        return $q->where('estado', 1);
    } 

}
