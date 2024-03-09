<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laboratorio;
use App\Models\Producto;
use Exception;
use Illuminate\Http\Request;

class LaboratorioController extends Controller
{
    public function index(Request $request){
        $searchTerm = $request->searchTerm;
        $data = new Laboratorio();

        if(isset($searchTerm)){
            $data = $data->where("nombre", "LIKE", "%{$searchTerm}%");
        }

        return $data->paginate($request->perPage);
    }
    
    public function store(Request $request){
        $this->validate($request, [
            'nombre' => 'required|string|max: 250',
        ]);
        Laboratorio::create($request->all());

        return response()->json(['success'=>true, 'message' => 'Laboratorio creado correctamente!',]);
    }
    
    public function update(Request $request, $id){
        $this->validate($request, [
            'nombre' => 'required|string|max: 250',
        ]);
        $data = Laboratorio::findOrFail($id);
        $data->update($request->all());

        return response()->json(['success'=>true, 'message' => 'Laboratorio actualizado correctamente!']);
    }
    
    public function destroy($id){
        try{
            //Relaciones: Productos
            //--- Comprobacion de Existencia de Operaciones ---
            $c_productos = Producto::where('id_laboratorio', $id)->count();
            if($c_productos > 0) return response()->json(["success"=>false, "message"=>"El laboratorio se encuentra registrado en operaciones."], 200);
            //--- Fin Comprobacion de Existencia de Operaciones ---
            
            $data = Laboratorio::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();

            return response()->json(["success"=>true], 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function laboratoriosCombo(){
        try{
            return Laboratorio::where([
                ['estado', 1]
            ])->get();            
            
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}