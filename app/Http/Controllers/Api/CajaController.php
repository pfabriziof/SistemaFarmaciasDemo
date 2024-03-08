<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\CajaDetalle;
use App\Models\MedioPago;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function index(Request $request){
        $authUser = auth('api')->user();

        $fechaInicio = $request->fechaInicio;
        $fechaFin    = $request->fechaFin;
        $data = Caja::where([
            ["id_sucursal", $authUser->id_sucursal],
        ]);

        if(isset($fechaInicio)){
            $fechaInicio = $fechaInicio .' 00:00:00';
            if(isset($fechaFin)){
                $fechaFin = $fechaFin .' 23:59:59';
                $data = $data->whereBetween('fecha_apertura', [$fechaInicio, $fechaFin]);

            }else{
                $data = $data->where('fecha_apertura', '>=', $fechaInicio);
            }
        }

        return $data->orderBy('fecha_apertura','desc')->paginate($request->perPage);
    }

    public function store(Request $request)
    {
        $authUser = auth('api')->user();
        $this->validate($request, [
            'monto_apertura' => 'required|integer',
        ]);

        Caja::create([
            'fecha_apertura'  => date('Y-m-d H:i:s'),
            'monto_apertura'  => $request->monto_apertura,
            'id_usuario'      => $authUser->id,
            'id_sucursal'     => $authUser->id_sucursal
        ]);

        return response()->json(['success'=>true, 'message' => 'Caja abierta correctamente!',]);
    }

    public function update(Request $request, $id)
    {
        //Cerrar Caja
        $caja= Caja::findOrFail($id);
        $caja_detalle = $request->caja_detalle;

        $monto_cierre = (float) $caja->monto_apertura;
        foreach ($caja_detalle as $value) {
            CajaDetalle::create([
                'id_caja'  => $id,
                'id_medio_pago' => $value["id_medio_pago"],
                'monto' =>$value["monto"]
            ]);

            $monto_cierre += (float) $value["monto"];
        }

        $upd_data = array(
            'fecha_cierre'  =>  date('Y-m-d H:i:s'),
            'monto_cierre'  => $monto_cierre,
        );
        $caja->update($upd_data);

        return response()->json(['success'=>true, 'message' => 'Caja cerrada correctamente!',]);
    }

    public function destroy($id)
    {
        //
    }

    public function getDetalleCaja($id){
        $detail = CajaDetalle::where('id_caja', $id)->get();
        return $detail;
    }

    public function cajaAbierta(){
        $authUser = auth('api')->user();

        $data = Caja::where([
            ["id_sucursal", $authUser->id_sucursal],
        ])
        ->whereNull("fecha_cierre")
        ->first();

        return $data;
    }
    public function getMontosDelDia(){
        $authUser = auth('api')->user();
        $mediosPago = MedioPago::all();
        $cajaAbierta = $this->cajaAbierta();
        if(!isset($cajaAbierta)) return array();
        
        $montosDelDia = array();
        foreach ($mediosPago as $medioPago) {
            $comprobantesCaja = DB::table('comprobantes')
            ->where('created_at', '>=', $cajaAbierta->fecha_apertura)
            ->where("id_medio_pago",$medioPago->id_medio_pago)
            ->where("id_sucursal",$authUser->id_sucursal)
            ->select(DB::raw('SUM(total) as total'))
            ->first();
            if($comprobantesCaja->total==null){
                $medioPago->monto = "0.00";
            }
            else{
                $medioPago->monto = $comprobantesCaja->total;
            }
            array_push($montosDelDia, $medioPago);  
        }
        
        return $montosDelDia;
    }
}
