<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UbigeoDistrito extends Model
{
    protected $table= 'ubigeo_peru_districts';
    protected $primaryKey= 'id';
    protected $fillable = [
        'province_id',
        'department_id',
        'name',
    ];
    public $timestamps = false;
}
