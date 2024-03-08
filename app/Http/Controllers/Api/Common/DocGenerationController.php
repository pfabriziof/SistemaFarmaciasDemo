<?php

namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Comprobante;
use App\Models\Egresos;
use App\Models\MedioPago;
use App\Models\OrdenCompra;
use App\Models\OrdenCompraDetalle;
use App\Models\ProveedorCotizacion;
use App\Models\ProveedorCotizacionDetalle;
use App\Models\Sucursal;
use App\Utils\EMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class DocGenerationController extends Controller
{
    //--- PDF Cotizacion Proveedor ---
    public function vistaProveedorCotizacion($id){
        $document = ProveedorCotizacion::find($id);
        $document_detail_pr = ProveedorCotizacionDetalle::join('productos_servicios', 'prv_cotizacion_detalle.id_producto', '=', 'productos_servicios.id_producto')
        ->where("id_cotizacion_prv", $id)
        ->where("productos_servicios.servicio", 0)->get();

        $document_detail_sv = ProveedorCotizacionDetalle::join('productos_servicios', 'prv_cotizacion_detalle.id_producto', '=', 'productos_servicios.id_producto')
        ->where("id_cotizacion_prv", $id)
        ->where("productos_servicios.servicio", 1)->get();

        return view('compras/prv_cotizacion_pdf',compact("document", "document_detail_pr", "document_detail_sv"));
    }
    public function generarProveedorCotizacionPDF($id, Mpdf $mpdf){
        $html =  $this->vistaProveedorCotizacion($id)->render();
        $filename = 'pdf_'.time().'.pdf';
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'I');
    }
    public function sendMailCotizacion(Request $request){
        //---Validacion de campos---
        $messages = [
            'to_email.required' => 'El campo email destinatario es requerido',
            'to_email.email'    => 'El formato email destinatario es inválido',
            
            'to_name.required'  => 'El campo nombre destinatario es requerido',
        ];
        $this->validate($request, [
            'to_email' => 'required|string|email',
            'to_name'  => 'required|string',
        ], $messages);
        //--- End ---

        $html =  $this->vistaProveedorCotizacion($request->id)->render();
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);

        EMailer::send('cotizacion_mail', $request->to_email,
            [
                "to_name"        => $request->to_name,
                "pdf_attachment" => $mpdf->Output('', 'S'),
            ]
        );

        return response()->json(['success'=>true, 'message' => 'El email con el archivo adjunto ha sido enviado correctamente',]);
    }
    //--- End ---

    //--- PDF Orden Compra ---
    public function vistaOrdenCompra($id){
        $document = OrdenCompra::find($id);
        $document_detail_pr = OrdenCompraDetalle::join('productos_servicios', 'orden_compra_detalle.id_producto', '=', 'productos_servicios.id_producto')
        ->where("id_orden_compra", $id)
        ->where("productos_servicios.servicio", 0)->get();

        $document_detail_sv = OrdenCompraDetalle::join('productos_servicios', 'orden_compra_detalle.id_producto', '=', 'productos_servicios.id_producto')
        ->where("id_orden_compra", $id)
        ->where("productos_servicios.servicio", 1)->get();

        return view('compras/ordcomp_pdf',compact("document", "document_detail_pr", "document_detail_sv"));
    }
    public function generarOrdenCompraPDF($id, Mpdf $mpdf){
        $html =  $this->vistaOrdenCompra($id)->render();
        $filename = 'pdf_'.time().'.pdf';
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'I');
    }
    public function sendMailOrdenCompra(Request $request){
        //---Validacion de campos---
        $messages = [
            'to_email.required' => 'El campo email destinatario es requerido',
            'to_email.email'    => 'El formato email destinatario es inválido',
            
            'to_name.required'  => 'El campo nombre destinatario es requerido',
        ];
        $this->validate($request, [
            'to_email' => 'required|string|email',
            'to_name'  => 'required|string',
        ], $messages);
        //--- End ---


        $html =  $this->vistaOrdenCompra($request->id)->render();
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);

        EMailer::send('orden_compra_mail', $request->to_email,
            [
                "to_name"        => $request->to_name,
                "pdf_attachment" => $mpdf->Output('', 'S'),
            ]
        );

        return response()->json(['success'=>true, 'message' => 'El email con el archivo adjunto ha sido enviado correctamente',]);
    }
    //--- End ---


    //--- Generar PDF Compra ---
    public function vistaCompra($id){
        $document = Compra::find($id);
        $document_detail_pr = CompraDetalle::join('productos_servicios', 'compras_detalle.id_producto', '=', 'productos_servicios.id_producto')
        ->where("id_compra", $id)
        ->where("productos_servicios.servicio", 0)->get();

        $document_detail_sv = CompraDetalle::join('productos_servicios', 'compras_detalle.id_producto', '=', 'productos_servicios.id_producto')
        ->where("id_compra", $id)
        ->where("productos_servicios.servicio", 1)->get();

        return view('compras/compra_pdf',compact("document", "document_detail_pr", "document_detail_sv"));
    }
    public function generarCompraPDF($id, Mpdf $mpdf){
        $html = $this->vistaCompra($id)->render();
        $filename = 'pdf_'.time().'.pdf';
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'I');
    }
    public function sendMailCompra(Request $request){
        //---Validacion de campos---
        $messages = [
            'to_email.required' => 'El campo email destinatario es requerido',
            'to_email.email'    => 'El formato email destinatario es inválido',
            
            'to_name.required'  => 'El campo nombre destinatario es requerido',
        ];
        $this->validate($request, [
            'to_email' => 'required|string|email',
            'to_name'  => 'required|string',
        ], $messages);
        //--- End ---


        $html =  $this->vistaCompra($request->id)->render();
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);

        EMailer::send('compra_mail', $request->to_email,
            [
                "to_name"        => $request->to_name,
                "pdf_attachment" => $mpdf->Output('', 'S'),
            ]
        );

        return response()->json(['success'=>true, 'message' => 'El email con el archivo adjunto ha sido enviado correctamente',]);
    }
    //--- End ---


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
    public function generarCajaPDF($id, Mpdf $mpdf){
        $html =  $this->vistaCaja($id)->render();
        $filename = 'pdf_'.time().'.pdf';
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'I');
    }
    //--- End ---
}
