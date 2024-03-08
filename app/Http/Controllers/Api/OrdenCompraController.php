<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListaPreciosDetalle;
use App\Models\OrdenCompra;
use App\Models\OrdenCompraDetalle;
use App\Models\Producto;
use Exception;
use Illuminate\Http\Request;

class OrdenCompraController extends Controller
{
    public function index(Request $request){
        $authUser = auth('api')->user();

        $searchTerm = $request->searchTerm;
        $fechaInicio = date($request->fechaInicio);
        $fechaFin    = date($request->fechaFin);
        $datos = OrdenCompra::where([
            ["id_sucursal", $authUser->id_sucursal],
        ]);

        if(isset($searchTerm)){
            $datos = $datos->whereHas('proveedor', function ($query) use ($searchTerm) {
                $query->where('nombre', 'like', "%{$searchTerm}%")
                      ->orWhere('nro_doc', 'like', "%{$searchTerm}%");
            });
        }
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $datos = $datos->whereBetween('fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $datos = $datos->where('fecha_emision', '>=', $fechaInicio);
            }
        }

        return $datos->latest()->paginate($request->perPage);
    }
    
    public function create(){
        //
    }
    
    public function store(Request $request){

        //---Validacion de campos---
        $this->validate($request, [
            'id_proveedor'      => 'required',
            'id_moneda'         => 'required',
            'id_medio_pago'     => 'required',
            'id_tipo_cambio'    => 'required',
            'email'             => 'required',
            'fecha_emision'     => 'required',
            'fecha_vencimiento' => 'required',
        ]);
        //--- End ---

        //--- Validacion de Detalle ---
        if(empty($request->ord_detalle)){
            return response()->json(['errors' => ['message' => ['El detalle debe tener al menos un producto']]], 500);
        }else{
            foreach($request->ord_detalle as $row){
                if(!$row["lista_detalle"] || !$row["cantidad"] || !$row["precio_unitario"]){
                    return response()->json(['errors' => ['message' => ['Faltan datos en el detalle de la venta']]], 500);
                }
            }
        }
        //--- End ---

        $authUser = auth('api')->user();
        $id_sucursal   = $authUser->id_sucursal;
        $id_usuario    = $authUser->id;
        
        //---Creacion Orden Compra---
        $data = new OrdenCompra();
        $data->id_usuario     = $id_usuario;
        $data->id_sucursal    = $id_sucursal;
        $data->id_proveedor   = $request->id_proveedor;
        $data->id_moneda      = $request->id_moneda;
        $data->id_medio_pago  = $request->id_medio_pago;
        $data->id_tipo_cambio = $request->id_tipo_cambio;
        $data->email          = $request->email;
        $data->fecha_emision  = $request->fecha_emision;
        $data->fecha_vencimiento = $request->fecha_vencimiento;
        //Verificacion Numeracion
        $last_ordcomp = OrdenCompra::latest()->first();
        $data->numeracion = $last_ordcomp != null? $last_ordcomp->numeracion + 1: 1;
        $data->save();
        //--- End ---

        //---Detalle Orden Compra---
        $id_orden_compra = $data->id_orden_compra;
        $sumaTotal = 0;

        $sum_gravadas  = 0;
        $op_inafectas  = 0;
        $op_exoneradas = 0;
        $total_icbper  = 0;

        foreach($request->ord_detalle as $row){
            $product_i = Producto::find($row["id_producto"]);
            $tipo_impuesto = $product_i->tipo_producto->impuesto;
            $u_icbper      = $product_i->tipo_producto->icbper;
            
            $listdet_i   = ListaPreciosDetalle::find($row["lista_detalle"]["id_lista_detalle"]);
            $listdet_id  = $listdet_i->id_lista_detalle;

            $detalle = new OrdenCompraDetalle();
            $detalle->id_orden_compra   = $id_orden_compra;
            $detalle->id_producto       = $product_i->id_producto;
            $detalle->nombre_producto   = $product_i->nombreProducto;
            $detalle->id_unidad_medida  = $product_i->id_unidad_medida;
            $detalle->und_simbolo       = $product_i->unidad_medida->simbolo;
            $detalle->id_lista_detalle  = $listdet_id;

            $detalle->cantidad         = $row["cantidad"];
            $detalle->precio_unitario  = (float) $row["precio_unitario"];  
            $detalle->precio_total     = (float) $row["precio_unitario"] * (float) $row["cantidad"];


            //--- SUMA SEGUN EL IMPUESTO ---
            switch ($tipo_impuesto) {
                case 1:
                    $sum_gravadas = (float) $sum_gravadas + (float) $detalle->precio_total;
                    break;

                case 2:
                    $op_inafectas = (float) $op_inafectas + (float) $detalle->precio_total;
                    break;

                case 3:
                    $op_exoneradas = (float) $op_exoneradas + (float) $detalle->precio_total;
                    break;

                case 4:
                    $sum_gravadas = (float) $sum_gravadas + (float) $detalle->precio_total;
                    $total_icbper = (float) $total_icbper + ((float) $u_icbper * (float) $detalle->cantidad);
                    break;
            }
            //--- END ---

            $detalle->save();
            $sumaTotal += (float) $detalle->precio_total;
        }
        
        //--- Actualizacion Totales ---
        $data->op_inafectas  = $op_inafectas;
        $data->op_exoneradas = $op_exoneradas;
        $data->icbper        = $total_icbper;

        $op_gravadas = $sum_gravadas/1.18;
        $data->op_gravadas   = $op_gravadas;
        
        $data->porcentaje_igv = 18;
        $data->igv            = $sum_gravadas - $op_gravadas;
        $data->total          = $sumaTotal + $total_icbper;
        $data->save();
        //--- End ---

        return ['message' => 'Orden creada, pendiente de aprobación.', 'id_orden_compra' => $id_orden_compra];
    }
    
    public function show($id){
        $data = OrdenCompra::find($id);
        $data_detalle = OrdenCompraDetalle::where('id_orden_compra',$id)->get();

        return response()->json(["orden_compra"=>$data, "orden_detalle"=>$data_detalle]);
    }
    
    public function edit($id){
        //
    }
    
    public function update(Request $request, $id){
        //
    }
    
    public function destroy($id){
        try{
            $data = OrdenCompra::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 2;
            $data->save();

            return response()->json($data, 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
