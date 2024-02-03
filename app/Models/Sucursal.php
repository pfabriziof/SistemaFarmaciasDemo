<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table= 'sucursales';
    protected $primaryKey= 'id_sucursal';
    protected $fillable = [
        'id_empresa',
        'nombre_sucursal',
        'cod_domicilio_fiscal',
        'direccion_fiscal',

        'id_departamento',
        'id_provincia',
        'id_distrito',
        
        'telefono',
        'direccion_comercial',
        'email',
        'direccion_web',
        'nro_cuenta_bancario',
        'cci_bancario',
        'api_url',
        'api_token',
        'estado',
    ];
    public $timestamps = true;

    protected $with = array('empresa', 'departamento', 'provincia', 'distrito');
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'id_empresa');
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
