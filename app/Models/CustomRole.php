<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CustomRole extends Role
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'title',
        'guard_name',

        'active',
        'status',
    ];
    public $timestamps = true;

    //--- Static Methods ---
    public function customRemove(){   
        try{            
            if($this->active != 1){
                $this->active = 1;
                $this->status = 1;

            } else {
                $this->active = 0;
                $this->status = 0;

            }
            $this->save();

        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 500);
        }  
    }

    public static function allowedToSave($name){
        $role =  CustomRole::where('name', $name)->first();
        if(isset($role)){
            throw new \Exception("Role {$name} already exist", 500);
        }
    }
    //--- End ---
}