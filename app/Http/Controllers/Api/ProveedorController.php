<?php

namespace App\Http\Controllers\Api;

use App\Models\Compra;
use App\Models\DeudaCompra;
use App\Http\Controllers\Controller;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\ProveedorCotizacion;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request) {
        $authUser = auth('api')->user();

        $searchTerm = $request->searchTerm;
        $datos = Proveedor::where([
            ["id_sucursal", $authUser->id_sucursal],
        ]);

        if(isset($searchTerm)){
            $datos = $datos->where(function ($query) use ($searchTerm) {
                $query->where('nombre', 'like', "%{$searchTerm}%")
                      ->orWhere('nro_doc', 'like', "%{$searchTerm}%");
            });
        }

        return $datos->latest()->paginate($request->perPage);
    }
    
    public function create()
    {
        //
    }
    
    public function store(Request $request){
        $authUser = auth('api')->user();

        //---Validacion de campos---
        $this->validate($request, [
            'direccion'      => 'required|string|max: 250',

            'id_departamento' => 'required|integer',
            'id_provincia'    => 'required|integer',
            'id_distrito'     => 'required|integer',

            'tipo_proveedor' => 'required|integer',
            'nombre'         => 'required|string|max: 250',
            'nro_doc'        => 'required|unique:App\Models\Proveedor,nro_doc',
            'id_tipo_doc'    => 'required|integer',
        ]);
        //---Fin ---

        Proveedor::create([
            'id_tipo_doc'  => $request->id_tipo_doc,
            'id_sucursal'  => $authUser->id_sucursal,
            'tipo_proveedor' => $request->tipo_proveedor,
            'nro_doc'      => $request->nro_doc,
            'nombre'       => $request->nombre,
            
            'id_departamento' => $request->id_departamento,
            'id_provincia'    => $request->id_provincia,
            'id_distrito'     => $request->id_distrito,
            
            'contacto_nombre'   => $request->contacto_nombre,
            'contacto_telefono' => $request->contacto_telefono,
            
            'email'        => $request->email,
            'telefono'     => $request->telefono,
            'direccion'    => $request->direccion,
        ]);

        return response()->json(['success'=>true, 'message' => 'Proveedor creado correctamente!']);
    }
    
    public function show($id)
    {
        //
    }
    
    public function edit($id)
    {
        //
    }
    
    public function update(Request $request, $id){
        //---Validacion de campos---
        $this->validate($request, [
            'direccion'      => 'required|string|max: 250',

            'id_departamento' => 'required|integer',
            'id_provincia'    => 'required|integer',
            'id_distrito'     => 'required|integer',

            'tipo_proveedor' => 'required|integer',
            'nombre'         => 'required|string|max: 250',
            'nro_doc'        => 'required|unique:App\Models\Proveedor,nro_doc,'.$id,
            'id_tipo_doc'    => 'required|integer',
        ]);
        //---Fin ---

        $proveedor = Proveedor::findOrFail($id);
        $upd_data = array(
            'id_tipo_doc'    => $request->id_tipo_doc,
            'tipo_proveedor' => $request->tipo_proveedor,
            'nro_doc'        => $request->nro_doc,
            'nombre'         => $request->nombre,
            
            'id_departamento' => $request->id_departamento,
            'id_provincia'    => $request->id_provincia,
            'id_distrito'     => $request->id_distrito,
            
            'contacto_nombre'   => $request->contacto_nombre,
            'contacto_telefono' => $request->contacto_telefono,
            
            'email'        => $request->email,
            'telefono'     => $request->telefono,
            'direccion'    => $request->direccion,
        );
        $proveedor->update($upd_data);

        return response()->json(['success'=>true, 'message' => 'Proveedor actualizado correctamente!']);
    }
    
    public function destroy($id){   
        try{
            //Relaciones: Compras, Deudas Compras, Orden Compra, Prv Cotizaciones, 
            //--- Comprobacion de Existencia de Operaciones ---
            $c_compras = Compra::where('id_proveedor', $id)->count();
            $c_deucomp = DeudaCompra::where('id_proveedor', $id)->count();
            $c_ordcomp = OrdenCompra::where('id_proveedor', $id)->count();
            $c_prvcotz = ProveedorCotizacion::where('id_proveedor', $id)->count();
            $suma = $c_compras + $c_deucomp +  $c_ordcomp + $c_prvcotz;
            
            if($suma > 0) return response()->json(["success"=>false, "message"=>"El proveedor se encuentra registrado en operaciones."], 200);
            //--- End ---

            $data = Proveedor::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();

            return response()->json(["success"=>true], 200);
        }catch (\Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }
    
    public function getProveedorDeuda($id) {
        $datos = Proveedor::find($id);
        return $datos;
    }
}
