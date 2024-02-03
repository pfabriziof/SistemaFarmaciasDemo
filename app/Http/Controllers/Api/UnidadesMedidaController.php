<?php

namespace App\Http\Controllers\Api;

use App\Models\ComprobanteDetalle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnidadMedida;

class UnidadesMedidaController extends Controller
{
    public function index(Request $request) {
        $searchTerm = $request->searchTerm;
        $datos = new UnidadMedida();

        if(isset($searchTerm)){
            $datos = $datos->where(function ($query) use ($searchTerm) {
                $query->where('unidad_medida', 'like', "%{$searchTerm}%")
                      ->orWhere('codigo_sunat', 'like', "%{$searchTerm}%");
            });
        }

        return $datos->paginate($request->perPage);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'codigo_sunat'  => 'required|string|max: 45',
            'simbolo'       => 'required|string|max: 45',
            'unidad_medida' => 'required|string|max: 45',
        ]);
        UnidadMedida::create($request->all());

        return response()->json(['success'=>true, 'message' => 'Unidad de medida creada correctamente!']);
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'codigo_sunat'  => 'required|string|max: 45',
            'simbolo'       => 'required|string|max: 45',
            'unidad_medida' => 'required|string|max: 45',
        ]);
        $unidad_medida = UnidadMedida::findOrFail($id);
        $unidad_medida->update( $request->all());

        return response()->json(['success'=>true, 'message' => 'Unidad de medida actualizada correctamente!']);
    }
    
    public function destroy($id)
    {
        try{
            //Relaciones: ComprobanteDetalle
            //--- Comprobacion de Existencia de Operaciones ---
            $c_inv_detail = ComprobanteDetalle::where('id_unidad_medida', $id)->count();
            $suma = $c_inv_detail;

            if($suma > 0) return response()->json(["success"=>false, "message"=>"La unidad de medida se encuentra registrada en operaciones."], 200);
            //--- End ---

            $data = UnidadMedida::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();

            return response()->json(["success"=>true], 200);

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function unidadesMedidaCombo(){
        try{
            return UnidadMedida::where([
                ['estado', 1]
            ])->get();
            
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
