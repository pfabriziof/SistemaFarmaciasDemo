<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table= 'tipo_documento';
    protected $primaryKey= 'id_tipo_doc';
    protected $fillable = [
        'tipo_documento',
        'codigo_sunat',
    ];
    public $timestamps = false;
}
