<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\Producto;
use Exception;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index(Request $request){
        $searchTerm = $request->searchTerm;
        $datos = new Marca();
        
        if(isset($searchTerm)){
            $datos = $datos->where("marca", "LIKE", "%{$searchTerm}%");
        }

        return $datos->paginate($request->perPage);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'marca' => 'required|string|max: 250',
        ]);
        Marca::create($request->all());

        return response()->json(['success'=>true, 'message' => 'Marca creada correctamente!']);
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'marca' => 'required|string|max: 250',
        ]);
        $data = Marca::findOrFail($id);
        $data->update($request->all());

        return response()->json(['success'=>true, 'message' => 'Marca actualizada correctamente!']);
    }
    
    public function destroy($id)
    {
        try{
            //Relaciones: Productos
            //--- Comprobacion de Existencia de Operaciones ---
            $c_productos = Producto::where('id_marca', $id)->count();
            if($c_productos > 0) return response()->json(["success"=>false, "message"=>"La marca se encuentra registrada en operaciones."], 200);
            //--- End ---
            
            $data = Marca::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();
            
            return response()->json(["success"=>true], 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function marcasCombo(){
        try{
            return Marca::where([
                ['estado', 1]
            ])->get();            
            
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}