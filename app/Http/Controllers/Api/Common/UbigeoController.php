<?php

namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use App\Models\UbigeoDepartamento;
use App\Models\UbigeoDistrito;
use App\Models\UbigeoProvincia;
use Illuminate\Http\Request;

class UbigeoController extends Controller
{
    public function getDepartments(){
        return UbigeoDepartamento::all();
    }

    public function getProvinces($id){
        return UbigeoProvincia::where('department_id', (int) $id)->get();
    }

    public function getDistricts(Request $request){
        return UbigeoDistrito::where([
            ['province_id', $request->province_id],
            ['department_id', $request->department_id]
        ])->get();
    }
}
