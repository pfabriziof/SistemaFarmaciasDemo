<?php

namespace App\Http\Controllers\Api;

use App\Models\Empresa;
use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use Exception;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(Request $request){
        $search_term = $request->searchTerm;
        $data = Empresa::where([
            ["id_empresa", $this->getEmpresaUsuarioSesion()->id_empresa],
        ]);
        
        if(isset($search_term)){
            $data = $data->where(function ($query) use ($search_term) {
                $query->where('nombre', 'like', "%{$search_term}%")
                      ->orWhere('ruc', 'like', "%{$search_term}%");
            });
        }

        return $data->latest()->paginate($request->perPage);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'ruc'    => 'required|string|max: 45',
            'nombre' => 'required|string|max: 250',
        ]);

        $data = Empresa::create([
            'ruc'    => $request->ruc,
            'nombre' => $request->nombre,
        ]);

        return response()->json(['success'=>true, 'message' => 'Empresa creada correctamente!', 'empresa'=>$data]);
    }
    
    public function update(Request $request, $id)
    {
        $data = Empresa::findOrFail($id);
        $this->validate($request, [
            'ruc'    => 'required|string|max: 45',
            'nombre' => 'required|string|max: 250',
        ]);

        $data->update($request->all());
        return response()->json(['success'=>true, 'message' => 'Empresa actualizada correctamente!', 'empresa'=>$data], 200);
    }
    
    public function destroy($id)
    {
        try{
            //Relaciones: Sucursales
            //--- Comprobacion de Existencia de Operaciones ---
            $c_sucursales = Sucursal::where('id_empresa', $id)->count();
            if($c_sucursales > 0) return response()->json(["success"=>false, "message"=>"La empresa se encuentra registrada en operaciones."], 200);
            //--- Fin Comprobacion de Existencia de Operaciones ---
            
            $data = Empresa::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();

            return response()->json(["success"=>true], 200);
            
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getEmpresaUsuarioSesion(){
        $auth_user = auth('api')->user();
        $sucursal = Sucursal::find($auth_user->id_sucursal);

    
        return Empresa::find($sucursal->id_empresa);
    }

    public function empresasCombo(){
        try{
            return Empresa::where([
                ['estado', 1]
            ])->get();            
            
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}