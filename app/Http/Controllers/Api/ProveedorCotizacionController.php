<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\ProveedorCotizacion;
use App\Models\ProveedorCotizacionDetalle;
use Exception;
use Illuminate\Http\Request;

class ProveedorCotizacionController extends Controller
{
    public function index(Request $request){
        $authUser   = auth('api')->user();

        $searchTerm = $request->searchTerm;
        $fechaInicio = date($request->fechaInicio);
        $fechaFin    = date($request->fechaFin);
        $datos = ProveedorCotizacion::where([
            ["id_sucursal", $authUser->id_sucursal],
        ]);

        if(isset($searchTerm)){
            $datos = $datos->whereHas('proveedor', function ($query) use ($searchTerm) {
                $query->where('nombre', 'like', "%{$searchTerm}%")
                      ->orWhere('nro_doc', "like", "%{$searchTerm}%");
            });
        }
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $datos = $datos->whereBetween('fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $datos = $datos->where('fecha_emision', '>=', $fechaInicio);
            }
        }

        return $datos->latest()->paginate($request->perPage);
    }
    
    public function create(){
        //
    }
    
    public function store(Request $request){
        //---Validacion de campos---
        $this->validate($request, [
            'id_proveedor'   => 'required',
            'email'          => 'required',
            'fecha_emision'  => 'required',
        ]);

        if(empty($request->productos)){
            return response()->json(['errors' => ['message' => ['El detalle debe tener al menos un producto']]], 500);
        }
        //---Fin Validacion de campos---

        $authUser = auth('api')->user();
        $id_sucursal   = $authUser->id_sucursal;
        $id_usuario    = $authUser->id;
        //---Creacion Cotizacion---
        $data = new ProveedorCotizacion();
        $data->id_usuario    = $id_usuario;
        $data->id_sucursal   = $id_sucursal;
        $data->id_proveedor  = $request->id_proveedor;
        $data->email         = $request->email;
        $data->fecha_emision = $request->fecha_emision;
        //Verificacion Numeracion
        $last_cotz = ProveedorCotizacion::latest()->first();
        $data->numeracion = $last_cotz != null? $last_cotz->numeracion + 1: 1;
        $data->save();
        //--- End ---

        //---Detalle Cotizacion---
        $id_cotizacion = $data->id_cotizacion_prv;
        foreach($request->productos as $pr){
            $product_i = Producto::find($pr["id_producto"]);

            $detalle = new ProveedorCotizacionDetalle();
            $detalle->id_cotizacion_prv = $id_cotizacion;
            $detalle->id_producto       = $product_i->id_producto;
            $detalle->nombre_producto   = $product_i->nombreProducto;
            $detalle->id_unidad_medida  = $product_i->unidad_medida->id_unidad_medida;  
            $detalle->und_simbolo       = $product_i->unidad_medida->simbolo;
            $detalle->cantidad          = $pr["cantidad"];
            $detalle->save();
        }
        //--- End ---

        return ['message' => 'Cotización de proveedor creada.', 'id_cotizacion' => $id_cotizacion];
    }
    
    public function show($id){
        $data = ProveedorCotizacion::find($id);
        $data_detail = ProveedorCotizacionDetalle::where('id_cotizacion_prv',$id)->get();

        return response()->json(["cotizacion" => $data, "cotizacion_detalle" => $data_detail]);
    }
    
    public function edit($id){
        //
    }
    
    public function update(Request $request, $id){
        //
    }
    
    public function destroy($id){
        try{
            $data = ProveedorCotizacion::findOrFail($id);
            $data->id_estado = $data->id_estado != 1? 1 : 2;
            $data->save();

            return response()->json($data, 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}