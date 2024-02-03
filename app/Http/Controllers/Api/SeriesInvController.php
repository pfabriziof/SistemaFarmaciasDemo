<?php

namespace App\Http\Controllers\Api;

use App\Models\Comprobante;
use App\Models\SerieInv;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeriesInvController extends Controller
{
    public function index(Request $request)
    {
        try{
            $searchTerm = $request->searchTerm;

            $data = new SerieInv();

            if($searchTerm != ''){
                $data = $data->where(function ($query) use ($searchTerm) {
                    $query->where('serie', 'like', "%{$searchTerm}%");
                });
            }

            return $data->latest()->paginate($request->perPage);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function store(Request $request)
    {
        try{
            SerieInv::create($request->all());
            
            return response()->json(['success'=>true, 'message' => 'Serie creada correctamente!']);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage(),500);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $data = SerieInv::findOrFail($id);
            $data->update($request->all());

            return response()->json(['success'=>true, 'message' => 'Serie actualizada correctamente!']);
            
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage(),500);
        }
    }

    public function destroy($id)
    {
        try{
            //Relaciones: Comprobantes
            //--- Comprobacion de Existencia de Operaciones ---
            $c_inv = Comprobante::where('id_serie', $id)->count();
            if($c_inv > 0) return response()->json(["success"=>false, "message"=>"Serie se encuentra registrada en operaciones."], 200);
            //--- End ---
            
            $data = SerieInv::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();

            return response()->json(["success"=>true], 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}