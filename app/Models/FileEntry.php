<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileEntry extends Model
{
    protected $table = 'file_paths';
    protected $primaryKey = 'id';
    protected $fillable = [
        'filename',
        'mime',
        'path',
        'size',
    ];
    
    public $timestamps = true;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
