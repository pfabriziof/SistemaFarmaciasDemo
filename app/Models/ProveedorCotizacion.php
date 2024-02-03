<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProveedorCotizacion extends Model
{
    protected $table= 'prv_cotizaciones';
    protected $primaryKey= 'id_cotizacion_prv';
    protected $fillable = [
        'id_proveedor',
        'id_usuario',
        'id_sucursal',
        'numeracion', 
        'email', 
        'fecha_emision',
        'id_estado',
    ];
    public $timestamps = true;

    protected $with = array('proveedor', 'usuario', 'sucursal', 'estado');

    public function usuario(){
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
    public function proveedor(){
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }
    public function estado(){
        return $this->belongsTo(CompraEstado::class, 'id_estado');
    }
}
