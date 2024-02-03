<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\ClienteDireccion;

class ClienteController extends Controller
{
    public function index(Request $request){
        $authUser = auth('api')->user();

        $searchTerm = $request->searchTerm;
        $datos = Cliente::where([
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
    
    public function store(Request $request)
    {
        $authUser = auth('api')->user();
        //--- Validacion de campos ---
        $this->validate($request, [
            'id_departamento' => 'required|integer',
            'id_provincia'    => 'required|integer',
            'id_distrito'     => 'required|integer',

            'tipo_cliente' => 'required|integer',
            'nombre'       => 'required|string|max: 250',
            'nro_doc'      => 'required|unique:App\Models\Cliente,nro_doc',
            'id_tipo_doc'  => 'required|integer',
        ]);

        if(empty($request->cli_direcciones)){
            return response()->json(['errors' => ['direccion' => ['Se debe ingresar la direccion del cliente']]], 500);
        }else{
            foreach($request->cli_direcciones as $cld){
                if(!$cld["direccion"]){
                    return response()->json(['errors' => ['direccion' => ['Falta especificar direccion del cliente']]], 500);
                }
            }
        }
        //--- End ---

        
        $cliente = Cliente::create([
            'id_tipo_doc'  => $request->id_tipo_doc,
            'id_sucursal'  => $authUser->id_sucursal,
            'nro_doc'      => $request->nro_doc,
            'nombre'       => $request->nombre,
            'tipo_cliente' => $request->tipo_cliente,

            'id_departamento' => $request->id_departamento,
            'id_provincia'    => $request->id_provincia,
            'id_distrito'     => $request->id_distrito,
            
            'contacto_nombre'   => $request->contacto_nombre,
            'contacto_telefono' => $request->contacto_telefono,
            
            'email'        => $request->email,
            'telefono'     => $request->telefono,
        ]);
        foreach($request->cli_direcciones as $cld){
            if($cld["direccion"]){
                ClienteDireccion::create([
                    'id_cliente' => $cliente->id_cliente,
                    'direccion'  => $cld["direccion"],
                ]);
            }
        }
        
        return response()->json(['success'=>true, 'message' => 'Cliente creado correctamente!', 'cliente'=>$cliente]);
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
        $cliente = Cliente::findOrFail($id);

        //---Validacion de campos---
        $this->validate($request, [
            'id_departamento' => 'required|integer',
            'id_provincia'    => 'required|integer',
            'id_distrito'     => 'required|integer',

            'tipo_cliente' => 'required|integer',
            'nombre'       => 'required|string|max: 250',
            'nro_doc'      => 'required|unique:App\Models\Cliente,nro_doc,'.$id,
            'id_tipo_doc'  => 'required|integer',
        ]);

        if(empty($request->cli_direcciones)){
            return response()->json(['errors' => ['direccion' => ['Se debe ingresar la direccion del cliente']]], 500);
        }else{
            foreach($request->cli_direcciones as $cld){
                if(!$cld["direccion"]){
                    return response()->json(['errors' => ['direccion' => ['Falta especificar direccion del cliente']]], 500);
                }
            }
        }
        //---Fin ---


        $upd_data = array(
            'id_tipo_doc'  => $request->id_tipo_doc,
            'nro_doc'      => $request->nro_doc,
            'nombre'       => $request->nombre,
            'tipo_cliente' => $request->tipo_cliente,

            'id_departamento' => $request->id_departamento,
            'id_provincia'    => $request->id_provincia,
            'id_distrito'     => $request->id_distrito,
            
            'contacto_nombre'   => $request->contacto_nombre,
            'contacto_telefono' => $request->contacto_telefono,
            
            'email'        => $request->email,
            'telefono'     => $request->telefono,
        );
        $cliente->update($upd_data);

        ClienteDireccion::where('id_cliente', $id)->update(['estado' => 0]);
        foreach($request->cli_direcciones as $row){
            if($row['id_direccion']){
                $lista_detalle = ClienteDireccion::find($row['id_direccion']);
                $lista_detalle->update([
                    "direccion" => $row['direccion'],
                    "estado"    => $row['estado'],
                ]);

            }else{
                ClienteDireccion::create([
                    'id_cliente' => $cliente->id_cliente,
                    'direccion'  => $row["direccion"],
                ]);
            }
        }
        return response()->json(['success'=>true, 'message' => 'Cliente actualizado correctamente!']);
    }
    
    public function destroy($id){
        $data = Cliente::findOrFail($id);
        $data->estado = $data->estado != 1? 1 : 0;
        $data->save();

        return response()->json(["success"=>true], 200);
    }

    public function getClientePredeterminado(){
        return Cliente::where('nombre', 'CLIENTES VARIOS')->first();
    }
    
    public function getClienteDeuda($id) {
        $datos = Cliente::find($id);
        return $datos;
    }
    public function getClienteDirecciones($id){
        $data = ClienteDireccion::where([
            ['id_cliente', $id],
            ['estado', 1]
        ])->get();
        return response()->json(["direcciones"=>$data]);
    }
}
