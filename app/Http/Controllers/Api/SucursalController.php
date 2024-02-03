<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sucursal;
use Exception;

class SucursalController extends Controller
{
    public function index(Request $request){
        $authUser = auth('api')->user();

        $searchTerm = $request->searchTerm;
        $data = Sucursal::where([
            ["id_empresa", $authUser->sucursal->id_empresa],
        ]);

        if(isset($searchTerm)){
            $data = $data->where(function ($query) use ($searchTerm) {
                $query->where('nombre_sucursal', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        return $data->latest()->paginate($request->perPage);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'id_distrito'     => 'required|integer',
            'id_provincia'    => 'required|integer',
            'id_departamento' => 'required|integer',
            
            'nombre_sucursal' => 'required|string|max: 250',
            // 'id_empresa'      => 'required|integer',
            // 'telefono'        => 'integer',
            // 'cod_domicilio_fiscal' => 'integer',
        ]);
        Sucursal::create($request->all());

        return response()->json(['success'=>true, 'message' => 'Sucursal creada correctamente!']);
    }

    public function update(Request $request, $id)
    {
        $sucursal = Sucursal::findOrFail($id);
        $this->validate($request, [
            'id_distrito'     => 'required|integer',
            'id_provincia'    => 'required|integer',
            'id_departamento' => 'required|integer',
            
            'nombre_sucursal' => 'required|string|max: 250',
            'id_empresa'      => 'required|integer',
            // 'telefono'        => 'integer',
            // 'cod_domicilio_fiscal' => 'integer',
        ]);

        $sucursal->update($request->all());

        return response()->json(['success'=>true, 'message' => 'Sucursal actualizada correctamente!']);
    }
    
    public function destroy($id)
    {
        try{
            $data = Sucursal::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();

            return response()->json($data, 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function sucursalesCombo(){
        try{
            $authUser = auth('api')->user();
            return Sucursal::where([
                ['id_empresa', $authUser->sucursal->empresa->id_empresa],
                ['estado', 1]
            ])->get();            
            
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
