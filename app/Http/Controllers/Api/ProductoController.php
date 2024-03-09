<?php

namespace App\Http\Controllers\Api;

use App\Models\AlmacenMovimiento;
use App\Models\CompraDetalle;
use App\Models\ComprobanteDetalle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\LoteProducto;
use App\Models\ListaPreciosDetalle;
use App\Models\OrdenCompraDetalle;
use App\Models\ProveedorCotizacionDetalle;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    public function index(Request $request){
        try {
            $authUser = auth('api')->user();
            $searchTerm = $request->searchTerm;

            $data = Producto::where([
                ["id_sucursal", $authUser->id_sucursal],
            ]);

            if (isset($searchTerm)) {
                $data = $data->where(function ($query) use ($searchTerm) {
                    $query->where('nombreProducto', 'like', "%{$searchTerm}%")
                          ->orWhere('codigo_producto', 'like', "%{$searchTerm}%");
                });
            }

            return $data->latest()->paginate($request->perPage);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function store(Request $request){
        // $messages = [
        //     'data.stock_minimo.required'     => 'El campo stock mínimo es requerido',
        //     'data.id_categoria.required'     => 'El campo categoria es requerido',
        //     'data.id_laboratorio.required'   => 'El campo laboratorio es requerido',
        //     'data.id_marca.required'         => 'El campo marca es requerido',
        //     'data.nombreProducto.required'   => 'El campo nombre producto es requerido',
        //     'data.codigo_producto.required'  => 'El campo codigo producto es requerido',
        // ];

        // //---Validacion de campos---
        // $this->validate($request, [
        //     'data.id_laboratorio'   => 'required|integer',
        //     'data.stock_minimo'     => 'required',
        //     'data.id_marca'         => 'required|integer',
        //     'data.nombreProducto'   => 'required|string|max: 250',
        //     'data.id_categoria'     => 'required|integer',
        //     'data.codigo_producto'  => 'required|string|max: 250',
        // ], $messages);
        
        //====Validacion de Lotes====
        foreach($request->lotes as $lt){
            if(!$lt["lote"] || !$lt["cantidad"] || !$lt["fecha_expiracion"]){
                return response()->json(['errors' => ['message' => ['Falta completar datos en la lista de lotes']]], 500);
            }
        }

        //====Validacion Listas de Precio====
        if(empty($request->list_detail)){
            return response()->json(['errors' => ['message' => ['Debe existir al menos una lista de precio especificada']]], 500);
        }else{
            foreach($request->list_detail as $row){
                if(!$row["id_lista_precio"] || !$row["precio_compra"] || !$row["precio_venta"] || !$row["unidades"]){
                    return response()->json(['errors' => ['message' => ['Faltan datos en la lista de precios']]], 500);
                }
            }
        }
        //--- End ---

        //--- Creacion de Lotes, Listas y Producto ---
        $user = auth('api')->user();
        $id_sucursal   = $user->id_sucursal;
        $id_usuario    = $user->id;
        
        $data = array_merge($request->data,['id_sucursal' => $id_sucursal]);
        $product = Producto::create($data);

        //lotes
        $stock_total = 0;         
        foreach ($request->lotes as $lote) {
            $data_lote = array_merge($lote, ['id_producto' => $product->id_producto], ['id_sucursal' => $id_sucursal]);
            LoteProducto::create($data_lote);
            $stock_total += $lote["cantidad"];
        }

        //list_detail
        foreach ($request->list_detail as $list) {
            $data_list = array_merge($list,['id_producto' => $product->id_producto], ['id_sucursal' => $id_sucursal]);
            ListaPreciosDetalle::create($data_list);
        }

        //Actualizando stock producto
        $product->update([
            "stock"=>$stock_total
        ]);
        //--- End ---

        if(empty($request->lotes)){
            $movimiento = new AlmacenMovimiento();
            $movimiento->id_sucursal = $id_sucursal;
            $movimiento->id_usuario  = $id_usuario;
            $movimiento->id_tipo_movimiento = 5; // Stock Inicial
            $movimiento->id_producto      = $product->id_producto;
            $movimiento->NombreProducto   = $product->nombreProducto;
            $movimiento->id_unidad_medida = 2; //Unidad
            $movimiento->und_simbolo      = "UND";

            $movimiento->cantidad         = $product->stock;
            $movimiento->precioUnitario   = null;
            $movimiento->precioTotal      = null;
            $movimiento->stock_actual     = $product->stock;
            $movimiento->fecha_movimiento = date('Y-m-d');
            $movimiento->save();
        }

        return response()->json(['success'=>true, 'message' => 'Producto creado correctamente!',]);
    }

    public function show($id){
        try{
            // get the user by ID
            $product = Producto::findOrFail($id);
            // If everything is ok : returns user data
            return response()->json($product,200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function update(Request $request, $id){
        $messages = [
            'data.stock_minimo.required'     => 'El campo stock mínimo es requerido',
            'data.id_categoria.required'     => 'El campo categoria es requerido',
            'data.id_laboratorio.required'   => 'El campo laboratorio es requerido',
            'data.id_marca.required'         => 'El campo marca es requerido',
            'data.nombreProducto.required'   => 'El campo nombre producto es requerido',
            'data.codigo_producto.required'  => 'El campo codigo producto es requerido',
        ];

        //---Validacion de campos---
        $this->validate($request, [
            'data.id_laboratorio'   => 'required|integer',
            'data.stock_minimo'     => 'required',
            'data.id_marca'         => 'required|integer',
            'data.nombreProducto'   => 'required|string|max: 250',
            'data.id_categoria'     => 'required|integer',
            'data.codigo_producto'  => 'required|string|max: 250',
        ], $messages);

        //====Validacion Listas de Precio====
        if(empty($request->list_detail)){
            return response()->json(['errors' => ['message' => ['Debe existir al menos una lista de precio especificada']]], 500);
        }else{
            foreach($request->list_detail as $row){
                if(!$row["id_lista_precio"] || !$row["precio_compra"] || !$row["precio_venta"] || !$row["unidades"]){
                    return response()->json(['errors' => ['message' => ['Faltan datos en la lista de precios']]], 500);
                }
            }
        }
        //--- End ---

        
        $auth_user = auth('api')->user();
        $producto = Producto::findOrFail($id);
        $producto->update($request->data);

        // --- Gestion de Listas de Precio ---
        ListaPreciosDetalle::where('id_producto', $id)->update(['estado' => 0]);
        foreach ($request->list_detail as $row) {
            $data_list = array_merge($row, ['id_producto' => $id], ['id_sucursal' => $auth_user->id_sucursal]);
            
            if($row['id_lista_detalle']){
                $lista_detalle = ListaPreciosDetalle::find($row['id_lista_detalle']);
                $lista_detalle->update([
                    "id_lista_precio" => $row['id_lista_precio'],
                    "precio_compra"   => $row['precio_compra'],
                    "precio_venta"    => $row['precio_venta'],
                    "unidades"        => $row['unidades'],
                    "estado"          => $row['estado'],
                ]);

            }else{
                ListaPreciosDetalle::create($data_list);
            }
        }
        // --- Fin ---
        
        return response()->json(['success'=>true, 'message' => 'Producto actualizado correctamente!']);
    }

    public function destroy($id){
        try {
            //Relaciones: Almacen Movimientos, Compras Detalle, Comprobante Detalle, Lista Precios Detalle, Lote Productos, Orden Compra Detalle, Prv Cotizacion Detalle, 
            //--- Comprobacion de Existencia de Operaciones ---
            $c_almmov  = AlmacenMovimiento::where('id_producto', $id)->count();
            $c_compdet = CompraDetalle::where('id_producto', $id)->count();
            $c_ventdet = ComprobanteDetalle::where('id_producto', $id)->count();
            $c_ordcdet = OrdenCompraDetalle::where('id_producto', $id)->count();
            $c_pcotzde = ProveedorCotizacionDetalle::where('id_producto', $id)->count();

            //Casos Especiales
            $c_lstprde = ListaPreciosDetalle::where('id_producto', $id)->count();
            $c_lotprod = LoteProducto::where('id_producto', $id)->count();

            $suma = $c_almmov + $c_compdet +  $c_ventdet +  $c_ordcdet +  $c_pcotzde;

            if($suma > 0) return response()->json(["success"=>false, "message"=>"El producto se encuentra registrado en operaciones."], 200);
            //--- End ---

            $data = Producto::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();

            return response()->json(["success"=>true], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}