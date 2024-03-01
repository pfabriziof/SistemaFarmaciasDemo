<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompressedTable extends Model
{
    use HasFactory;

    protected $table = 'compressed_tables';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'table_name',
        'query',
        'prompt',
    ];
    public $timestamps = false;
}
