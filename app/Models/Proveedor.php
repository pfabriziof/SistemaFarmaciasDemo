<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table= 'proveedores';
    protected $primaryKey= 'id_proveedor';
    protected $fillable = [
        'id_tipo_doc',
        'id_sucursal',
        'tipo_proveedor',
        'nombre',
        'nro_doc',
        'direccion',
        'email',
        'telefono',
        
        'contacto_nombre',
        'contacto_telefono',

        'id_departamento',
        'id_provincia',
        'id_distrito',

        'estado',
    ];
    public $timestamps = true;

    protected $with = array('sucursal', 'tipo_doc');

    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
    public function tipo_doc(){
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_doc');
    }
}
