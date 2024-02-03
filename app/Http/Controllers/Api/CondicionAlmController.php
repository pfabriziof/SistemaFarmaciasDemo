<?php

namespace App\Http\Controllers\Api;

use App\Models\CondicionAlmacenamiento;
use App\Http\Controllers\Controller;
use App\Models\Producto;
use Exception;
use Illuminate\Http\Request;

class CondicionAlmController extends Controller
{
    public function index(Request $request){
        $searchTerm = $request->searchTerm;
        $datos = new CondicionAlmacenamiento;

        if(isset($searchTerm)){
            $datos = $datos->where("descripcion", "LIKE", "%{$searchTerm}%");
        }

        return $datos->paginate($request->perPage);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'descripcion' => 'required|string|max: 250',
        ]);
        CondicionAlmacenamiento::create($request->all());
        
        return response()->json(['success'=>true, 'message' => 'Condición de almacenamiento creada correctamente!']);
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'descripcion' => 'required|string|max: 250',
        ]);
        $data = CondicionAlmacenamiento::findOrFail($id);
        $data->update($request->all());

        return response()->json(['success'=>true, 'message' => 'Condición de almacenamiento actualizada correctamente!']);
    }
    
    public function destroy($id)
    {
        try{
            //Relaciones: Productos
            //--- Comprobacion de Existencia de Operaciones ---
            $c_productos = Producto::where('id_condicion_alm', $id)->count();
            if($c_productos > 0) return response()->json(["success"=>false, "message"=>"La condición de almacenamiento se encuentra registrada en operaciones."], 200);
            //--- Fin Comprobacion de Existencia de Operaciones ---

            $data = CondicionAlmacenamiento::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();
            
            return response()->json(["success"=>true], 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
    
    public function condicionesAlmCombo(){
        try{
            return CondicionAlmacenamiento::where([
                ['estado', 1]
            ])->get();            
            
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
