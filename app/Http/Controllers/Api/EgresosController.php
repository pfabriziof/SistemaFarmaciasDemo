<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Egresos;

class EgresosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $authUser = auth('api')->user();
        
        $fechaInicio = $request->fechaInicio;
        $fechaFin    = $request->fechaFin;
        $datos = Egresos::where([
            ["id_sucursal", $authUser->id_sucursal],
            ["estado", 1],
        ]);

        if(isset($fechaInicio)){
            $fechaInicio = $fechaInicio .' 00:00:00';
            if(isset($fechaFin)){
                $fechaFin    = $fechaFin .' 23:59:59';
                $datos = $datos->whereBetween('fecha_egreso', [$fechaInicio, $fechaFin]);

            }else{
                $datos = $datos->where('fecha_egreso', '>=', $fechaInicio);
            }
        }

        return $datos->orderBy('fecha_egreso','desc')->paginate($request->perPage);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'id_tipo_egreso'  => 'required|integer',
            'id_motivo_egreso'=> 'required|integer',
            'metodo_gasto' => 'required|integer',
            'monto'           => 'required',
        ]);
        
        $authUser = auth('api')->user();
        
        Egresos::create([
            'id_usuario'    => $authUser->id,
            'id_sucursal'   => $authUser->id_sucursal,
            'id_tipo_egreso'    => $request->id_tipo_egreso,
            'id_motivo_egreso'  => $request->id_motivo_egreso,
            'metodo_gasto'   => $request->metodo_gasto,
            'fecha_egreso'  => date('Y-m-d H:i:s'),
            'monto'         => $request->monto,
            'detalle'       => $request->detalle,
        ]);

        return response()->json(['success'=>true, 'message' => 'Egreso creado correctamente!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
