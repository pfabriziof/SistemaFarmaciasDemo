<?php

namespace App\Http\Controllers\Api;

use App\Models\AlmacenMovimiento;
use App\Http\Controllers\Controller;

use App\Models\Cliente;
use App\Models\ClienteDireccion;
use App\Models\Comprobante;
use App\Models\ComprobanteDetalle;
use App\Models\DeudaComprobante;
use App\Models\DeudaComprobantePago;
use App\Models\ListaPreciosDetalle;
use App\Models\Producto;
use App\Models\LoteProducto;
use Illuminate\Http\Request;

class ComprobanteController extends Controller
{
    public function index(Request $request){
        // $startTime = microtime(true); // Capture start time

        $authUser = auth('api')->user();

        $searchTerm = $request->searchTerm;
        $fechaInicio = date($request->fechaInicio);
        $fechaFin    = date($request->fechaFin);
        $tipoDoc     = $request->id_tipo_comprobante;
        $estado    = $request->id_estado;
        $data = Comprobante::where([
            ["id_sucursal", $authUser->id_sucursal],
        ]);
        
        if(isset($searchTerm)){
            $data = $data->where(function ($query) use ($searchTerm) {
                $query->where('nombreCliente', 'like', "%{$searchTerm}%")
                      ->orWhere('nroDocCliente', 'like', "%{$searchTerm}%");
            });
        }
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $data = $data->whereBetween('fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $data = $data->where('fecha_emision', '>=', $fechaInicio);
            }
        }
        if(isset($tipoDoc)){
            $data = $data->where('id_tipo_comprobante', $tipoDoc);
        }
        if(isset($estado)){
            $data = $data->where('id_estado_comprobante', $estado);
        }

        // $endTime = microtime(true); // Capture end time
        // $executionTime = $endTime - $startTime;
        // Log::debug("Comprobante Index: Tiempo de ejecución: " . $executionTime . " segs");

        return $data->latest()->paginate($request->perPage);
    }

    public function store(Request $request){
        // $startTime = microtime(true); // Capture start time

        // ==== Validacion de campos ====
        $this->validate($request, [
            'id_tipo_comprobante' => 'required|integer',
            'id_serie'            => 'required|integer',
            'id_medio_pago'       => 'required|integer',
            'id_moneda'           => 'required|integer',
            'formato_impresion'   => 'required|integer',
            'id_direccion'        => 'required|integer',
            'fecha_emision'       => 'required',
            'fecha_vencimiento'   => 'required',
        ]);
        //--- Validacion Deuda Generada ---
        if($request->deuda_generada){
            if($request->deuda_adelanto > $request->inv_total){
                return response()->json(['errors' => ['deuda_adelanto' => ['El adelanto de la deuda es mayor al total de la venta']]], 500);
            }
        }
        //--- End ---
        //--- Validacion de Detalle ---
        if(empty($request->inv_detalle)){
            return response()->json(['errors' => ['message' => ['El detalle debe tener al menos un producto']]], 500);
        }else{
            foreach($request->inv_detalle as $row){
                if(!$row["lote"] || !$row["lista_detalle"] || !$row["precio_unitario"] || !$row["cantidad"]){
                    return response()->json(['errors' => ['message' => ['Faltan datos en el detalle de la venta']]], 500);
                }
            }
        }
        //--- End ---Stock
        //--- Validacion de Stock ---
        function searchForId($id, $array) {
            foreach ($array as $key => $val) {
                if ($val['id_producto'] === $id) {
                    return $key;
                }
            }
            return null;
        }
        
        $validacion_stock = array();
        foreach($request->inv_detalle as $row){
            $lote_i = $row["lote"];
            $id_producto = $lote_i["id_producto"];

            $search = searchForId($id_producto, $validacion_stock);
            if($search===null){
                $validacion_stock[]=array(
                    "id_producto" => $id_producto,
                    "cantidad"    => (float) $row["cantidad"],
                    "stock"       => (float) $lote_i["cantidad"],
                );
                if($validacion_stock[0]["cantidad"] > $validacion_stock[0]["stock"]){
                    return response()->json(['errors' => ['message' => [
                        'Cantidad de '.$row["nombre_producto"].' mayor a stock de lote '.$lote_i["lote"]
                    ]]], 500);
                }
            }else{
                $validacion_stock[$search]["cantidad"] = (float)$validacion_stock[$search]["cantidad"] + $row["cantidad"];

                if($validacion_stock[$search]["cantidad"] > $validacion_stock[$search]["stock"]){
                    return response()->json(['errors' => ['message' => ['Cantidad de '.$row["nombre_producto"].' mayor a stock']]], 500);
                }
            }
        }
        //--- End ---
        // ==== Fin ====

        
        $user = auth('api')->user();
        $cliente       = Cliente::find($request->cliente["id_cliente"]);
        $cliente_dir   = ClienteDireccion::find($request->id_direccion);
        $fecha_emision = date('Y-m-d');
        $id_sucursal   = $user->id_sucursal;
        $id_usuario    = $user->id;

        // --- Creacion del Comprobante ---
        $comprobante = new Comprobante();
        $comprobante->id_cliente            = $cliente->id_cliente;
        $comprobante->id_tipo_comprobante   = $request->id_tipo_comprobante;
        $comprobante->id_usuario            = $id_usuario;
        $comprobante->id_sucursal           = $id_sucursal;
        $comprobante->id_serie              = $request->id_serie;
        $comprobante->id_moneda             = $request->id_moneda;
        $comprobante->id_estado_comprobante = 4;
        $comprobante->id_medio_pago         = $request->id_medio_pago;
        $comprobante->id_tipo_cambio        = $request->id_tipo_cambio;
        
        $comprobante->nombreCliente         = $cliente->nombre;
        $comprobante->nroDocCliente         = $cliente->nro_doc;
        $comprobante->direccionCliente      = $cliente_dir->direccion;
        $comprobante->fecha_emision         = $fecha_emision;
        $comprobante->fecha_vencimiento     = $request->fecha_vencimiento;
        $comprobante->comentario            = $request->comentario;
        switch ($request->formato_impresion) {
            case 1:
                $comprobante->formato_impresion = 'ticket';
                break;
            case 2:
                $comprobante->formato_impresion = 'a4';
                break;
            case 3: 
                $comprobante->formato_impresion = 'a5';
                break;
        }
        $last_inv = Comprobante::where("id_serie", $request->id_serie)->latest()->first();
        $comprobante->correlativo = $last_inv != null ? $last_inv->correlativo + 1 :  1;
        $comprobante->save();
        //--- End ---
        
        //--- Construccion del Detalle ---
        $id_comprobante = $comprobante->id_comprobante;
        $sumaTotal = 0;

        $sum_gravadas  = 0;
        $op_inafectas  = 0;
        $op_exoneradas = 0;
        $total_icbper  = 0;

        foreach($request->inv_detalle as $row){
            $product_i     = Producto::find($row["id_producto"]);
            $lote_i        = LoteProducto::find($row["lote"]["id_lote"]);
            $tipo_impuesto = $product_i->tipo_producto->impuesto;
            $u_icbper      = $product_i->tipo_producto->icbper;

            $listdet_i   = ListaPreciosDetalle::find($row["lista_detalle"]["id_lista_detalle"]);
            $listdet_id  = $listdet_i->id_lista_detalle;
            $listdet_und = $listdet_i->unidades;

            $detalle = new ComprobanteDetalle();
            $detalle->id_comprobante   = $id_comprobante;
            $detalle->id_producto      = $product_i->id_producto;
            $detalle->nombre_producto  = $product_i->nombreProducto;
            $detalle->id_unidad_medida = $product_i->id_unidad_medida;
            $detalle->und_simbolo      = $product_i->unidad_medida->simbolo;
            $detalle->id_lista_detalle = $listdet_id;
            
            $detalle->id_lote          = $lote_i->id_lote;
            $detalle->lote_producto    = $lote_i->lote;
            
            $detalle->cantidad         = (float) $listdet_und * (float) $row["cantidad"];
            $detalle->cantidad_visual  = $row["cantidad"];
            $detalle->precio_unitario  = $row["precio_unitario"];
            $detalle->precio_total     = (float) $row["precio_unitario"] * (float) $row["cantidad"];

            if($product_i->servicio!=1){//Producto no es servicio
                $product_i->stock = (float) $product_i->stock - (float) $detalle->cantidad;
                $product_i->save();
            }

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
            

            //--- Gestion de lote ---
            $lote_i->cantidad = (float) $lote_i->cantidad - $detalle->cantidad;
            $lote_i->save();
            //--- End ---


            //--- Gestion de movimiento en almacen ---
            $movimiento = new AlmacenMovimiento();
            $movimiento->id_sucursal = $id_sucursal;
            $movimiento->id_usuario  = $id_usuario;
            $movimiento->id_tipo_movimiento = 2; // Salida
            $movimiento->id_producto      = $detalle->id_producto;
            $movimiento->NombreProducto   = $detalle->nombre_producto;
            $movimiento->id_unidad_medida = $detalle->id_unidad_medida;
            $movimiento->und_simbolo      = $detalle->und_simbolo;

            $movimiento->cantidad         = $detalle->cantidad;
            $movimiento->precioUnitario   = $detalle->precio_unitario;
            $movimiento->precioTotal      = $detalle->precio_total;
            $movimiento->stock_actual     = $product_i->stock;
            $movimiento->fecha_movimiento = date('Y-m-d');
            $movimiento->save();
            // --- End ---
        }
        //--- End ---

        //--- Actualizacion Campos Comprobante ---
        $comprobante->op_inafectas  = $op_inafectas;
        $comprobante->op_exoneradas = $op_exoneradas;
        $comprobante->icbper        = $total_icbper;

        $op_gravadas = $sum_gravadas/1.18;
        $comprobante->op_gravadas   = $op_gravadas;
        
        $comprobante->porcentaje_igv = 18;
        $comprobante->igv            = $sum_gravadas - $op_gravadas;
        $comprobante->total          = $sumaTotal + $total_icbper;

        $comprobante->time_elapsed = $request->time_elapsed;
        $comprobante->save();
        //--- End ---

        //--- Creacion de la deuda ---
        if($request->deuda_generada && $cliente->id_cliente!=1){
            $deuda_comp = new DeudaComprobante();
            $deuda_comp->id_comprobante        = $id_comprobante;
            $deuda_comp->id_cliente            = $cliente->id_cliente;
            $deuda_comp->total_adelanto        = $request->deuda_adelanto != null? $request->deuda_adelanto : 0;
            $deuda_comp->total_deuda           = $sumaTotal;
            $deuda_comp->total_monto_pagado    = $request->deuda_adelanto != null? $request->deuda_adelanto : 0;
            $deuda_comp->total_monto_pendiente = (float) $sumaTotal - (float) $deuda_comp->total_adelanto;
            $deuda_comp->fecha                 = $fecha_emision;
            $deuda_comp->save();

            if($request->deuda_adelanto != null){
                $pago_deuda_comp = new DeudaComprobantePago();
                $pago_deuda_comp->id_deuda     = $deuda_comp->id_deuda;
                $pago_deuda_comp->monto_pagado = $deuda_comp->total_monto_pagado;
                $pago_deuda_comp->comentario   = '';
                $pago_deuda_comp->fecha        = $fecha_emision;
                $pago_deuda_comp->save();
            }
        }

        // $endTime = microtime(true); // Capture end time
        // $executionTime = $endTime - $startTime;
        // Log::debug("Comprobante Create: Tiempo de ejecución: " . $executionTime . " segs");

        return response()->json(['success'=>true, 'message' => 'Comprobante creado correctamente!', 'id_comprobante' => $comprobante->id_comprobante]);
    }

    public function show($id){
        $data = Comprobante::find($id);
        $data_detalle = ComprobanteDetalle::where('id_comprobante',$id)->get();

        return response()->json(["comprobante"=>$data, "comprobante_detalle"=>$data_detalle]);
    }

    public function update(Request $request, $id){
        //
    }
    
    public function destroy($id, Request $request){
        // $startTime = microtime(true); // Capture start time

        $this->validate($request, [
            'motivo_anulacion' => 'required',
        ]);
        $comprobante = Comprobante::find($id);
        $upd_data = array(
            'id_estado_comprobante' => 2,
            'motivo_anulacion'  =>  $request->motivo_anulacion,
            'fecha_anulacion'  =>  date('Y-m-d'),
        );
        $comprobante->update($upd_data);

        $user = auth('api')->user();
        $id_sucursal = $user->id_sucursal;
        $id_usuario  = $user->id;
        $data_detalle = ComprobanteDetalle::where("id_comprobante", $id)->get();

        foreach($data_detalle as $dt_i){
            $product_i = Producto::find($dt_i->id_producto);

            $lote_update = LoteProducto::find($dt_i->id_lote);
            $lote_update->cantidad = (float) $lote_update->cantidad + (float) $dt_i->cantidad;
            $lote_update->save();
            
            if($product_i->servicio!=1){//Producto no es servicio
                $product_i->stock = (float) $product_i->stock + (float) $dt_i->cantidad;
            }
            $product_i->save();

            //Creacion del movimiento en almacen
            $movimiento = new AlmacenMovimiento();
            $movimiento->id_sucursal = $id_sucursal;
            $movimiento->id_usuario  = $id_usuario;
            $movimiento->id_tipo_movimiento = 3; // Devolucion
            $movimiento->id_producto      = $dt_i->id_producto;
            $movimiento->NombreProducto   = $dt_i->nombre_producto;
            $movimiento->id_unidad_medida = $dt_i->id_unidad_medida;
            $movimiento->und_simbolo      = $dt_i->und_simbolo;

            $movimiento->cantidad         = $dt_i->cantidad;
            $movimiento->precioUnitario   = $dt_i->precio_unitario;
            $movimiento->precioTotal      = $dt_i->precio_total;
            $movimiento->stock_actual     = $product_i->stock;
            $movimiento->fecha_movimiento = date('Y-m-d');
            $movimiento->save();
        }

        // $endTime = microtime(true); // Capture end time
        // $executionTime = $endTime - $startTime;
        // Log::debug("Comprobante Delete: Tiempo de ejecución: " . $executionTime . " segs");

        return ['message' => 'Comprobante anulado.'];
    }

    public function enviarComprobanteSunat($id_comprobante){
        $comprobante         = Comprobante::find($id_comprobante);
        $comprobante_detalle = ComprobanteDetalle::where("id_comprobante", $id_comprobante)->get();
        $items = array();

        //Array Api - Boleta Gravada Items[]
        foreach($comprobante_detalle as $dt_i){
            $product_i = Producto::find($dt_i->id_producto );
            $tipo_impuesto = $product_i->tipo_producto->impuesto;
            $u_icbper      = $product_i->tipo_producto->icbper;

            //--- DATOS SEGUN EL IMPUESTO ---
            switch ($tipo_impuesto) {
                case 1:
                    // sum_gravadas
                    $valor_unitario = $dt_i->precio_unitario / 1.18;
                    $total_base_igv = $dt_i->precio_total / 1.18;
                    $total_igv      = $dt_i->precio_total - $total_base_igv;
                    $codigo_afectacion = "10";
                    $total_bolsa_plastica = 0;
                    break;

                case 2:
                    // op_inafectas
                    $valor_unitario = $dt_i->precio_unitario;
                    $total_base_igv = $dt_i->precio_total;
                    $total_igv      = 0;
                    $codigo_afectacion = "30";
                    $total_bolsa_plastica = 0;
                    break;

                case 3:
                    // op_exoneradas
                    $valor_unitario = $dt_i->precio_unitario;
                    $total_base_igv = $dt_i->precio_total;
                    $total_igv      = 0;
                    $codigo_afectacion = "20";
                    $total_bolsa_plastica = 0;
                    break;

                case 4:
                    // icbper
                    $valor_unitario = $dt_i->precio_unitario/ 1.18;
                    $total_base_igv = $dt_i->precio_total/ 1.18;
                    $total_igv      = $dt_i->precio_total - $total_base_igv;
                    $codigo_afectacion = "10";
                    $total_bolsa_plastica = $u_icbper * $dt_i->cantidad;
                    break;
            }
            //--- END ---

            $items[] = array(
                "codigo_interno"             => $product_i->codigo_producto,
                "descripcion"                => $product_i->nombreProducto,
                "codigo_producto_sunat"      => "",
                "codigo_producto_gsl"        => "",
                "unidad_de_medida"           => $product_i->unidad_medida->codigo_sunat,
                "cantidad"                   => $dt_i->cantidad,
                "valor_unitario"             => number_format($valor_unitario, 2, '.', ''),
                "codigo_tipo_precio"         => "01",
                "precio_unitario"            => number_format($dt_i->precio_unitario, 2, '.', ''),
                "codigo_tipo_afectacion_igv" => $codigo_afectacion,
                "total_base_igv"             => number_format($total_base_igv, 2, '.', ''),
                "porcentaje_igv"             => 18,
                "total_igv"                  => number_format($total_igv, 2, '.', ''),
                "total_impuestos_bolsa_plastica"=> $total_bolsa_plastica,
                "total_impuestos"            => number_format($total_igv, 2, '.', ''),
                "total_valor_item"           => number_format($total_base_igv, 2, '.', ''),
                "total_item"                 => $dt_i->precio_total,
            );
        }

        //Array Api - Boleta Gravada
        $codigo_tipo_documento           = $comprobante->tipo_comprobante->codigo_sunat;
        $codigo_tipo_documento_identidad = $comprobante->cliente->tipo_doc->codigo_sunat;
        $hora_de_emision = date("h:i:s");

        $total_valor = (float) $comprobante->op_gravadas + (float) $comprobante->icbper;
        $comp_arr = array(
            "serie_documento"        => $comprobante->serie->serie,
            // "numero_documento"       => $comprobante->correlativo,
            "numero_documento"       => 1,
            "fecha_de_emision"       => $comprobante->fecha_emision,
            "hora_de_emision"        => $hora_de_emision, //extraer hora de emision de request
            "codigo_tipo_operacion"  => "0101", //Operacion Sunat
            "codigo_tipo_documento"  => $codigo_tipo_documento,
            "codigo_tipo_moneda"     => "PEN",
            "fecha_de_vencimiento"   => $comprobante->fecha_vencimiento,
            "numero_orden_de_compra" => "", //Opcional
            "formato_pdf"            => $comprobante->formato_impresion,
            "datos_del_emisor" => array(
                "codigo_pais"                      => "PE",
                "ubigeo"                           => "040101",
                "direccion"                        => "Av. Ejercito 303",
                "correo_electronico"               => "emisor@email.com", 
                "telefono"                         => "123456", 
                "codigo_del_domicilio_fiscal"      => "0000"
            ),
            // "documento_afectado" => array(
            //     "external_id"    => $comprobante->external_id,
            // ),
            "datos_del_cliente_o_receptor" => array(
                "codigo_tipo_documento_identidad"    => $codigo_tipo_documento_identidad,
                "numero_documento"                   => $comprobante->nroDocCliente,
                "apellidos_y_nombres_o_razon_social" => $comprobante->nombreCliente,
                "codigo_pais"                        => "PE",
                "ubigeo"                             => "",
                "direccion"                          => $comprobante->direccionCliente,
                "correo_electronico"                 => "", //Incluir correo
                "telefono"                           => ""  //Incluir telefono
            ),
            "totales" => array(
                "total_exportacion"            => 0.00,
                "total_operaciones_gravadas"   => number_format($comprobante->op_gravadas, 2, '.', ''),
                "total_operaciones_inafectas"  => number_format($comprobante->op_inafectas, 2, '.', ''),
                "total_operaciones_exoneradas" => number_format($comprobante->op_exoneradas, 2, '.', ''),
                "total_operaciones_gratuita"   => 0.00,
                "total_impuestos_bolsa_plastica" => $comprobante->icbper,
                "total_igv"                    => number_format($comprobante->igv,2, '.', ''),
                "total_impuestos"              => number_format($comprobante->igv,2, '.', ''),
                "total_valor"                  => $total_valor,
                "total_venta"                  => $comprobante->total,
            ),
            "items" => $items
        );

        //--- Envio de Json a la API - Boleta Gravada ---
        $comp_sunat = json_encode($comp_arr);
        
        $authUser = auth('api')->user();
        $token = $authUser->sucursal->api_token;
        $ruta = $authUser->sucursal->api_url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta."/api/documents");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$comp_sunat);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);//
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//
        $respuesta  = curl_exec($ch);
        if(curl_errno($ch)){
            echo 'Request Error:' . curl_error($ch);
        }
        curl_close($ch);
        //--- End ---


        $res_decode = json_decode($respuesta, true);
        if($res_decode['success'] == true){
            $external_id   = $res_decode['data']['external_id'];
            $pdf_document  = $res_decode['links']['pdf'];

            $comprobante->external_id = $external_id;
            $comprobante->id_estado_comprobante  = 1;
            $comprobante->save();
            
            return response(['success'=> true, 'message' => 'Comprobante validado.', 'id_comprobante' => $id_comprobante]);
        }else{
            $comprobante->id_estado_comprobante  = 3;
            $comprobante->save();
            
            return response(['success'=> false, 'errors' => ['message' => [$res_decode["message"], 'sunat_error']]], 500);
        }
    }
}