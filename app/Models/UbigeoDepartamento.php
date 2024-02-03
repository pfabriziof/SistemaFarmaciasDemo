<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UbigeoDepartamento extends Model
{
    protected $table= 'ubigeo_peru_departments';
    protected $primaryKey= 'id';
    protected $fillable = [
        'name',
    ];
    public $timestamps = false;
}
