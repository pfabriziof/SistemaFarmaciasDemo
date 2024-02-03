<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Egresos extends Model
{
    protected $table= 'egresos';
    protected $primaryKey= 'id_egreso';
    protected $fillable = [
        'id_sucursal',
        'id_usuario',
        'id_compra',

        'id_tipo_egreso',
        'id_motivo_egreso',
        'metodo_gasto',
        
        'fecha_egreso',
        'monto',
        'detalle',
        'estado',
    ];
    public $timestamps = false;

    protected $with = array('usuario', 'tipo_egreso', 'motivo_egreso');

    public function usuario(){
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function tipo_egreso(){
        return $this->belongsTo(TipoEgreso::class, 'id_tipo_egreso');
    }
    public function motivo_egreso(){
        return $this->belongsTo(EgresoMotivo::class, 'id_motivo_egreso');
    }

}
