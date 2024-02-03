<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UbigeoProvincia extends Model
{
    protected $table= 'ubigeo_peru_provinces';
    protected $primaryKey= 'id';
    protected $fillable = [
        'department_id',
        'name',
    ];
    public $timestamps = false;
}
