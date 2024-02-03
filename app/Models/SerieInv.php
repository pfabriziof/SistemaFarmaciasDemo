<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerieInv extends Model
{
    protected $table = 'series_inv';
    protected $primaryKey = 'id_serie';
    protected $fillable = [
        'id_sucursal',
        'id_tipo_comprobante',
        'serie',
        'estado',
    ];
    public $timestamps = true;

    protected $with = array('sucursal', 'tipo');

    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
    public function tipo(){
        return $this->belongsTo(TipoComprobante::class, 'id_tipo_comprobante');
    }
}
