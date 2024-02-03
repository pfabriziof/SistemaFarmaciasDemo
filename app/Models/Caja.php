<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table= 'caja';
    protected $primaryKey= 'id_caja';
    protected $fillable = [
        'fecha_apertura',
        'fecha_cierre',
        'monto_apertura',
        'monto_cierre',
        'id_usuario',
        'id_sucursal',
    ];
    public $timestamps = false;
    

    protected $with = array('usuario', 'sucursal');

    public function usuario(){
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
}