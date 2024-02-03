<?php

namespace App\Http\Controllers\Api;

use App\Models\AlmacenMovimiento;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlmacenMovimientosController extends Controller
{
    public function index(Request $request){
        $authUser = auth('api')->user();

        $searchTerm = $request->searchTerm;
        $fechaInicio = date($request->fechaInicio);
        $fechaFin    = date($request->fechaFin);

        $datos = AlmacenMovimiento::where([
            ['id_sucursal', $authUser->id_sucursal],
        ]);

        if(isset($searchTerm)){
            $datos = $datos->where(function ($query) use ($searchTerm) {
                $query->where('nombreProducto', 'like', "%{$searchTerm}%");
            });
        }
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $datos = $datos->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);
            }else{
                $datos = $datos->where('fecha_movimiento', '>=', $fechaInicio);
            }
        }

        return $datos->latest()->paginate($request->perPage);
    }
    
    public function create(){
        //
    }
    
    public function store(Request $request){
        //
    }

    public function show($id){
        //
    }
    
    public function edit($id){
        //
    }
    
    public function update(Request $request, $id){
        //
    }
    
    public function destroy($id)
    {
        //
    }
}