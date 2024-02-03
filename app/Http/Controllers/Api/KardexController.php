<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KardexController extends Controller
{
    public function getKardexValorizado(Request $request){
        $authUser = auth('api')->user();

        $searchTerm = $request->searchTerm;
        $fechaInicio  = $request->fechaInicio;
        $fechaFin    = $request->fechaFin;

        $datos = DB::table('productos_servicios')
            ->join('lista_precios_detalle', 'productos_servicios.id_producto', '=', 'lista_precios_detalle.id_producto')
            ->join('comprobante_detalle', 'productos_servicios.id_producto', '=', 'comprobante_detalle.id_producto')
            ->join('comprobantes', 'comprobante_detalle.id_comprobante', '=', 'comprobantes.id_comprobante')
            ->join('productos_categorias', 'productos_servicios.id_categoria', '=', 'productos_categorias.id_categoria')
            ->join('marcas_productos', 'productos_servicios.id_marca', '=', 'marcas_productos.id_marca')
            ->join('unidades_medida', 'comprobante_detalle.id_unidad_medida', '=', 'unidades_medida.id_unidad_medida')
            ->where('comprobantes.id_sucursal', $authUser->id_sucursal)
            ->where('productos_servicios.estado', 1)
            ->where('lista_precios_detalle.id_lista_precio', 1)
            ->where('lista_precios_detalle.estado', 1)
            ->select(DB::raw('
                productos_servicios.nombreProducto,
                productos_categorias.categoria,
                marcas_productos.marca,
                unidades_medida.unidad_medida,
                SUM(comprobante_detalle.cantidad) as und_vendido,
                precio_compra as costo_unitario,
                SUM(comprobante_detalle.precio_total) as valor_ventas,
                TRUNCATE(SUM(comprobante_detalle.cantidad) * precio_compra, 2) as costo_producto,
                TRUNCATE(SUM(comprobante_detalle.precio_total) - (SUM(comprobante_detalle.cantidad) * precio_compra), 2) as unidad_valorizada
            '));

        if(isset($searchTerm)){
            $datos = $datos->where('productos_servicios.nombreProducto','like', "%{$searchTerm}%");
        }
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $datos = $datos->whereBetween('comprobantes.fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $datos = $datos->where('comprobantes.fecha_emision', '>=', $fechaInicio);
            }
        }

        return $datos->groupBy('productos_servicios.id_producto')->paginate($request->perPage);
    }
}