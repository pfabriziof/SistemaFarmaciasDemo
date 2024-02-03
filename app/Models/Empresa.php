<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table= 'empresas';
    protected $primaryKey= 'id_empresa';
    protected $fillable = [
        'nombre',
        'ruc',
        'id_file',
        'estado',
    ];
    public $timestamps = true;

    protected $with = array('file_path');
    public function file_path(){
        return $this->belongsTo(FileEntry::class, 'id_file');
    }
}
