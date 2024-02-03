<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ListaPrecios;
use Exception;

class ListaPreciosController extends Controller
{
    public function index(Request $request) {
        $searchTerm = $request->searchTerm;
        $datos = new ListaPrecios();

        if(isset($searchTerm)){
            $datos = $datos->where(function ($query) use ($searchTerm) {
                $query->where('nombre', 'like', "%{$searchTerm}%")
                      ->orWhere('codigo', 'like', "%{$searchTerm}%");
            });
        }

        return $datos->paginate($request->perPage);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max: 250',
            'codigo' => 'required|string|max: 45',
        ]);
        ListaPrecios::create($request->all());
        
        return response()->json(['success'=>true, 'message' => 'Lista precio creada correctamente!',]);
    }
    
    public function show($id)
    {
        $data = ListaPrecios::find($id);
        return $data;
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max: 250',
            'codigo' => 'required|string|max: 45',
        ]);
        $price_list = ListaPrecios::findOrFail($id);
        $price_list->update( $request->all());

        return response()->json(['success'=>true, 'message' => 'Lista precio actualizada correctamente!',]);
    }
    
    public function destroy($id)
    {
        try {
            $data = ListaPrecios::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();

            return response()->json(["success"=>true], 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function priceListsCombo(){
        try{
            return ListaPrecios::where([
                ['estado', 1]
            ])->get();            
            
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}