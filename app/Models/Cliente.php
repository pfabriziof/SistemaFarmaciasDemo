<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table= 'clientes';
    protected $primaryKey= 'id_cliente';
    protected $fillable = [
        'id_cliente',
        'id_tipo_doc',
        'id_sucursal',
        'nombre',
        'nro_doc',
        'email',
        'telefono',
        
        'contacto_nombre',
        'contacto_telefono',
        'tipo_cliente',
        
        'id_departamento',
        'id_provincia',
        'id_distrito',
     
        'estado',
    ];
    
    public $timestamps = true;

    protected $with = array('sucursal', 'tipo_doc', 'direcciones', 'departamento', 'provincia', 'distrito');

    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
    public function tipo_doc(){
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_doc');
    }

    public function direcciones(){
        return $this->hasMany(ClienteDireccion::class, 'id_cliente');
    }
    public function departamento(){
        return $this->belongsTo(UbigeoDepartamento::class, 'id_departamento');
    }
    public function provincia(){
        return $this->belongsTo(UbigeoProvincia::class, 'id_provincia');
    }
    public function distrito(){
        return $this->belongsTo(UbigeoDistrito::class, 'id_distrito');
    }
}
