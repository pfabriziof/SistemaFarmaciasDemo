<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\CajaDetalle;
use App\Models\Compra;
use App\Models\Comprobante;
use App\Models\Egresos;
use App\Models\MedioPago;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

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

    //--- Generar PDF Caja ---
    public function vistaCaja($id){
        $document = Caja::find($id);

        //--- Pagos Efectivo / Tarjeta / Deposito ---
        $authUser = auth()->user();
        $sucursal = Sucursal::find($authUser->id_sucursal);
        $medios_pago = MedioPago::all();

        $document_detail = array();
        foreach ($medios_pago as $val) {
            $data_monto = DB::table('comprobantes')
                                ->where([
                                    ['created_at', '>=', $document->fecha_apertura],
                                    ["id_sucursal", $authUser->id_sucursal],
                                    ['id_estado_comprobante', 1]
                                ]);
            if(isset($document->fecha_cierre)){
                $data_monto = $data_monto->where('created_at', '<=', $document->fecha_cierre);
            }
            $data_monto = $data_monto
                ->where("id_medio_pago",$val->id_medio_pago)
                ->where("id_sucursal",$sucursal->id_sucursal)
                ->select(DB::raw('SUM(total) as total'))
                ->first();
            if($data_monto->total==null){
                $val->monto = "0.00";
            }
            else{
                $val->monto = $data_monto->total;
            }
            array_push($document_detail, $val);  
        }
        //--- End ---
        
        //--- Detalle de Ventas ---
        //SELECT * FROM comprobantes WHERE created_at >= '2021-03-17 09:49:59' AND created_at <= '2021-03-18 11:21:14'
        $document_sales = Comprobante::where([
            ['created_at', '>=', $document->fecha_apertura],
            ["id_sucursal", $authUser->id_sucursal],
            ['id_estado_comprobante', 1]
        ]);
        if(isset($document->fecha_cierre)){
            $document_sales = $document_sales->where('created_at', '<=', $document->fecha_cierre);
        }
        $document_sales = $document_sales->where("id_sucursal",$sucursal->id_sucursal);
        $sum_sales = $document_sales->sum('total');
        $document_sales = $document_sales->get();
        //--- End ---

        //--- Detalle de Compras ---
        $document_purchases = Compra::where([
            ['created_at', '>=', $document->fecha_apertura],
            ["id_sucursal", $authUser->id_sucursal],
            ['id_estado', 1]
        ]);
        if(isset($document->fecha_cierre)){
            $document_purchases = $document_purchases->where('created_at', '<=', $document->fecha_cierre);
        }
        $sum_purchases = $document_purchases->sum('total');
        $document_purchases = $document_purchases->get();
        //--- End ---

        //--- Total egresos ---
        $sum_egresos = Egresos::where([
            ['id_compra', null],
            ['fecha_egreso', '>=', $document->fecha_apertura],
            ['estado', 1],
        ]);
        if(isset($document->fecha_cierre)){
            $sum_egresos = $sum_egresos->where('fecha_egreso', '<=', $document->fecha_cierre);
        }

        $sum_egresos = $sum_egresos->where("id_sucursal",$sucursal->id_sucursal)->sum('monto');
        $sum_purchases += $sum_egresos;
        //--- End ---

        return view('caja/caja_pdf',compact("document", "sucursal", "document_detail", "document_sales", "sum_sales", "document_purchases", "sum_purchases"));
    }
    public function generarCajaPDF($id){
        $html =  $this->vistaCaja($id)->render();
        $filename = 'pdf_'.time().'.pdf';
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'I');
    }
    //--- End ---
}
