<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaDetalle extends Model
{
    protected $table= 'caja_detalle';
    protected $primaryKey= 'id_caja_det';
    protected $fillable = [
        'id_caja',
        'id_medio_pago',
        'monto',
    ];
    public $timestamps = false;
    

    protected $with = array('caja', 'medio_pago');

    public function caja(){
        return $this->belongsTo(Caja::class, 'id_caja');
    }
    public function medio_pago(){
        return $this->belongsTo(MedioPago::class, 'id_medio_pago');
    }
}
