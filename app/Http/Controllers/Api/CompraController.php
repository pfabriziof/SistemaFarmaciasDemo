<?php

namespace App\Http\Controllers\Api;

use App\Models\AlmacenMovimiento;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\DeudaCompra;
use App\Models\DeudaCompraPago;
use App\Models\Egresos;
use App\Http\Controllers\Controller;
use App\Models\ListaPreciosDetalle;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\LoteProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        // $startTime = microtime(true); // Capture start time

        $authUser   = auth('api')->user();

        $searchTerm = $request->searchTerm;
        $fechaInicio = date($request->fechaInicio);
        $fechaFin    = date($request->fechaFin);
        $estadoCompra = $request->estadoCompra;
        $datos = Compra::where([
            ["id_sucursal", $authUser->id_sucursal],
        ]);

        if(isset($searchTerm)){
            $datos = $datos->where(function ($query) use ($searchTerm) {
                $query->where('nombreProveedor', 'like', "%{$searchTerm}%")
                      ->orWhere('nroDocProveedor', 'like', "%{$searchTerm}%");
            });
        }
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $datos = $datos->whereBetween('fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $datos = $datos->where('fecha_emision', '>=', $fechaInicio);
            }
        }

        //Reporte Compras
        if(isset($estadoCompra)){
            $datos = $datos->where('id_estado', $estadoCompra);
        }

        // $endTime = microtime(true); // Capture end time
        // $executionTime = $endTime - $startTime;
        // Log::debug("Compra Index: Tiempo de ejecución: " . $executionTime . " segs");

        return $datos->latest()->paginate($request->perPage);
    }

    public function create(){
        //
    }

    public function store(Request $request){
        // $startTime = microtime(true); // Capture start time

        //--- Validacion de campos ---
        $this->validate($request, [
            'id_tipo_cambio'    => 'required',
            'id_medio_pago'     => 'required',
            'id_moneda'         => 'required',
            'origen_dinero'     => 'required|integer',

            'fecha_vencimiento' => 'required',
            'fecha_emision'     => 'required',
            'nro_factura'       => 'required|string|max: 45',
            'serie_factura'     => 'required|string|max: 45',
            'id_tipo_comprobante' => 'required|integer',

            'nro_guia_remision' => 'max: 45',
            'email'             => 'required',
            'id_proveedor'      => 'required',
        ]);
        if($request->deuda_generada){
            if($request->deuda_adelanto > $request->comp_total){
                return response()->json(['errors' => ['deuda_adelanto' => ['El adelanto de la deuda es mayor al total de la compra']]], 500);
            }
        }
        if(empty($request->compra_detalle)){
            return response()->json(['errors' => ['detalle_compra' => ['El detalle debe tener al menos un producto.']]], 500);
        }else{
            foreach($request->compra_detalle as $row){
                if(!$row["lista_detalle"] || !$row["cantidad"] || !$row["precio_unitario"] || !$row["lote"] || !$row["lote_fecha_exp"]){
                    return response()->json(['errors' => ['detalle_compra' => ['Faltan datos en el detalle de la compra.']]], 500);
                }
            }
        }
        //---Fin ---

        $authUser = auth('api')->user();
        $id_proveedor  = $request->id_proveedor;
        $proveedor     = Proveedor::find($request->id_proveedor);
        $fecha_emision = $request->fecha_emision;
        $id_sucursal   = $authUser->id_sucursal;
        $id_usuario    = $authUser->id;

        //--- Creacion de Compra ---
        $compra = new Compra();
        $compra->id_usuario        = $id_usuario;
        $compra->id_sucursal       = $id_sucursal;
        $compra->id_proveedor      = $id_proveedor;
        $compra->id_moneda         = $request->id_moneda;
        $compra->id_medio_pago     = $request->id_medio_pago;
        $compra->id_tipo_cambio    = $request->id_tipo_cambio;
        $compra->id_tipo_comprobante = $request->id_tipo_comprobante;
        $compra->origen_dinero     = $request->origen_dinero;
        $compra->nombreProveedor   = $proveedor->nombre;
        $compra->nroDocProveedor   = $proveedor->nro_doc;
        $compra->email             = $request->email;
        $compra->nro_guia_remision = $request->nro_guia_remision;
        $compra->serie_factura     = $request->serie_factura;
        $compra->nro_factura       = $request->nro_factura;
        $compra->fecha_emision     = $fecha_emision;
        $compra->fecha_vencimiento = $request->fecha_vencimiento;
        //Verificacion el correlativo
        $last_compr = Compra::latest()->first();
        $compra->correlativo = $last_compr != null ? $last_compr->correlativo + 1 : 1;
        //--- End ---
        $compra->deuda_generada = $request->deuda_generada;
        $compra->deuda_adelanto = $request->deuda_adelanto;
        $compra->save();
        $id_compra = $compra->id_compra;


        $sum_gravadas  = 0;
        $op_inafectas  = 0;
        $op_exoneradas = 0;
        $total_icbper  = 0;

        $sumaTotal = 0;
        foreach($request->compra_detalle as $row){
            $product_i = Producto::find($row["id_producto"]);
            $tipo_impuesto = $product_i->tipo_producto->impuesto;
            $u_icbper      = $product_i->tipo_producto->icbper;

            $listdet_i   = ListaPreciosDetalle::find($row["lista_detalle"]["id_lista_detalle"]);
            $listdet_id  = $listdet_i->id_lista_detalle;
            $listdet_und = $listdet_i->unidades;

            $detalle = new CompraDetalle();
            $detalle->id_compra       = $id_compra;
            $detalle->id_producto     = $product_i->id_producto;
            $detalle->nombre_producto = $product_i->nombreProducto;
            $detalle->id_unidad_medida  = $product_i->id_unidad_medida;
            $detalle->und_simbolo       = $product_i->unidad_medida->simbolo;
            $detalle->id_lista_detalle  = $listdet_id;

            $detalle->lote_name      = $row["lote"];
            $detalle->lote_fecha_exp = $row["lote_fecha_exp"];

            $detalle->cantidad        = (float) $listdet_und * (float) $row["cantidad"];
            $detalle->cantidad_visual = $row["cantidad"];
            $detalle->precio_unitario = $row["precio_unitario"];
            $detalle->precio_total    = (float) $row["precio_unitario"] * (float) $row["cantidad"];


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
        $compra->op_inafectas  = $op_inafectas;
        $compra->op_exoneradas = $op_exoneradas;
        $compra->icbper        = $total_icbper;

        $op_gravadas = $sum_gravadas/1.18;
        $compra->op_gravadas = $op_gravadas;

        $compra->porcentaje_igv = 18;
        $compra->igv            = $sum_gravadas - $op_gravadas;
        $compra->total          = $sumaTotal + $total_icbper;
        $compra->save();
        //--- End ---

        // $endTime = microtime(true); // Capture end time
        // $executionTime = $endTime - $startTime;
        // Log::debug("Compra Create: Tiempo de ejecución: " . $executionTime . " segs");

        return ['message' => 'Compra creada, pendiente de aprobación.', 'id_compra' => $compra->id_compra];
    }

    public function show($id){
        $data = Compra::find($id);
        $data_detalle = CompraDetalle::where('id_compra',$id)->get();

        return response()->json(["compra"=>$data, "compra_detalle"=>$data_detalle]);
    }

    public function edit($id){
        //
    }

    public function update(Request $request, $id){
        //
    }

    public function destroy($id){
        // $startTime = microtime(true); // Capture start time

        $user = auth('api')->user();
        $id_sucursal = $user->id_sucursal;
        $id_usuario  = $user->id;

        $compra = Compra::find($id);
        $compra->id_estado = $compra->id_estado != 1? 1 : 2;
        $compra->save();
        $compra_detalle = CompraDetalle::where("id_compra", $id)->get();

        //--- COMPRA ANULADA ---
        if($compra->id_estado==2){
            $compra->fecha_anulacion = date('Y-m-d');
            $compra->save();

            foreach($compra_detalle as $detalle_i){
                // --- Gestion Stock Producto ---
                $product_i = Producto::find($detalle_i->id_producto);
                if($product_i->servicio != 1){ // Producto no es servicio
                    $product_i->stock = (float) $product_i->stock - (float) $detalle_i->cantidad;
                    $product_i->save();
                }
                //--- End ---

                //--- Gestion Lote ---
                $lote = LoteProducto::find($detalle_i->lote_id);
                $lote->cantidad = 0;
                $lote->estado = 0;
                $lote->save();
                //--- End ---

                //--- Gestion Movimiento Almacen ---
                $movimiento = new AlmacenMovimiento();
                $movimiento->id_sucursal = $id_sucursal;
                $movimiento->id_usuario  = $id_usuario;
                $movimiento->id_tipo_movimiento = 3; // Devolucion
                $movimiento->id_producto      = $detalle_i->id_producto;
                $movimiento->NombreProducto   = $detalle_i->nombre_producto;
                $movimiento->id_unidad_medida = $detalle_i->id_unidad_medida;
                $movimiento->und_simbolo      = $detalle_i->und_simbolo;

                $movimiento->cantidad         = $detalle_i->cantidad;
                $movimiento->precioUnitario   = $detalle_i->precio_unitario;
                $movimiento->precioTotal      = $detalle_i->precio_total;
                $movimiento->stock_actual     = $product_i->stock;
                $movimiento->fecha_movimiento = date('Y-m-d');
                $movimiento->save();
                //--- End ---
            }

            //--- Gestion de Deuda ---
            $deuda = DeudaCompra::find($compra->deuda_id);
            if($deuda){
                $deuda->estado = 0;
                $deuda->save();
            }
            //--- End ---

            //--- Anulacion Egreso Caja Chica ---
            $egreso = Egresos::where('id_compra', $id)->first();
            if($egreso){
                $egreso->estado = 0;
                $egreso->save();
            }
            //--- End ---

        //--- COMPRA APROBADA ---
        } else if ($compra->id_estado==1) {

            foreach($compra_detalle as $detalle_i){
                // --- Gestion Stock Producto ---
                $product_i = Producto::find($detalle_i->id_producto);
                if($product_i->servicio != 1){ // Producto no es servicio
                    $product_i->stock = (float) $product_i->stock + (float) $detalle_i->cantidad;
                    $product_i->save();
                }
                //--- End ---

                //--- Gestion Lote ---
                $new_lote = new LoteProducto();
                $new_lote->id_producto = $product_i->id_producto;
                $new_lote->id_sucursal = $id_sucursal;
                $new_lote->lote        = $detalle_i->lote_name;
                $new_lote->fecha_expiracion = $detalle_i->lote_fecha_exp;
                $new_lote->cantidad    = $detalle_i->cantidad;
                $new_lote->save();

                $detalle_i->lote_id = $new_lote->id_lote;
                $detalle_i->save();
                //--- End ---

                //--- Gestion Movimiento Almacen ---
                $movimiento = new AlmacenMovimiento();
                $movimiento->id_sucursal = $id_sucursal;
                $movimiento->id_usuario  = $id_usuario;
                $movimiento->id_tipo_movimiento = 1; // Entrada
                $movimiento->id_producto      = $detalle_i->id_producto;
                $movimiento->NombreProducto   = $detalle_i->nombre_producto;
                $movimiento->id_unidad_medida = $detalle_i->id_unidad_medida;
                $movimiento->und_simbolo      = $detalle_i->und_simbolo;

                $movimiento->cantidad         = $detalle_i->cantidad;
                $movimiento->precioUnitario   = $detalle_i->precio_unitario;
                $movimiento->precioTotal      = $detalle_i->precio_total;
                $movimiento->stock_actual     = $product_i->stock;
                $movimiento->fecha_movimiento = date('Y-m-d');
                $movimiento->save();
                //--- End ---
            }

            //--- Gestion de Deuda ---
            if($compra->deuda_generada == 1){
                $new_deuda = new DeudaCompra();
                $new_deuda->id_compra             = $compra->id_compra;
                $new_deuda->id_proveedor          = $compra->id_proveedor;
                $new_deuda->total_adelanto        = $compra->deuda_adelanto != null? $compra->deuda_adelanto : 0;
                $new_deuda->total_deuda           = $compra->total;
                $new_deuda->total_monto_pagado    = $compra->deuda_adelanto != null? $compra->deuda_adelanto : 0;
                $new_deuda->total_monto_pendiente = (float) $compra->total - (float) $compra->deuda_adelanto;
                $new_deuda->fecha                 = date('Y-m-d');
                $new_deuda->save();

                if($compra->deuda_adelanto != null){
                    $pago_deuda = new DeudaCompraPago();
                    $pago_deuda->id_deuda     = $new_deuda->id_deuda;
                    $pago_deuda->monto_pagado = $new_deuda->total_monto_pagado;
                    $pago_deuda->fecha        = $new_deuda->fecha;
                    $pago_deuda->save();
                }

                $compra->deuda_id = $new_deuda->id_deuda;
                $compra->save();
            }
            //--- End ---

            //--- Egreso Caja Chica ---
            if($compra->origen_dinero == 1){
                Egresos::create([
                    'id_sucursal' => $compra->id_sucursal,
                    'id_usuario'  => $compra->id_usuario,
                    'id_compra'   => $compra->id_compra,

                    'id_tipo_egreso'   => 4,
                    'id_motivo_egreso' => 3,
                    'metodo_gasto'  => 1,

                    'fecha_egreso' => date('Y-m-d H:i:s'),
                    'monto'        => $compra->total,
                ]);
            }
            //--- End ---
        }

        // $endTime = microtime(true); // Capture end time
        // $executionTime = $endTime - $startTime;
        // Log::debug("Compra Delete: Tiempo de ejecución: " . $executionTime . " segs");

        return response()->json($compra, 200);
    }
}
