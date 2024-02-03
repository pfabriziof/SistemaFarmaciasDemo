<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductoCategoria;
use App\Models\Producto;
use Exception;

class CategoryController extends Controller
{
    public function index(Request $request){
        $searchTerm = $request->searchTerm;
        $datos = new ProductoCategoria();

        if(isset($searchTerm)){
            $datos = $datos->where(function ($query) use ($searchTerm) {
                $query->where('categoria', 'like', "%{$searchTerm}%")
                      ->orWhere('codigo', 'like', "%{$searchTerm}%");
            });
        }
        
        return $datos->paginate($request->per_page);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'categoria' => 'required|string|max: 250',
            'codigo'    => 'required|string|max: 45',
        ]);
        ProductoCategoria::create($request->all());
            
        return response()->json(['success'=>true, 'message' => 'Categoría creada correctamente!']);
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'categoria' => 'required|string|max: 250',
            'codigo'    => 'required|string|max: 45',
        ]);
        $category = ProductoCategoria::findOrFail($id);
        $category->update( $request->all());
        
        return response()->json(['success'=>true, 'message' => 'Categoría actualizada correctamente!']);
    }
    
    public function destroy($id)
    {
        try {
            //Relaciones: Productos
            //--- Comprobacion de Existencia de Operaciones ---
            $c_productos = Producto::where('id_categoria', $id)->count();
            if($c_productos > 0) return response()->json(["success"=>false, "message"=>"La categoría de producto se encuentra registrada en operaciones."], 200);
            //--- Fin Comprobacion de Existencia de Operaciones ---

            $data = ProductoCategoria::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();

            return response()->json(["success"=>true], 200);

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function categoriasCombo(){
        try{
            return ProductoCategoria::where([
                ['estado', 1]
            ])->get();
            
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
