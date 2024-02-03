<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuentasController extends Controller
{
    public function getCuentasCobrar(Request $request){
        $authUser = auth('api')->user();

        $searchTerm = $request->searchTerm;
        $fechaInicio = date($request->fechaInicio);
        $fechaFin    = date($request->fechaFin);

        $datos = DB::table('deudas_comprobantes')
            ->join('comprobantes', 'deudas_comprobantes.id_comprobante', '=', 'comprobantes.id_comprobante')
            ->join('clientes', 'deudas_comprobantes.id_cliente', '=', 'clientes.id_cliente')

            ->join('series_inv', 'comprobantes.id_serie', '=', 'series_inv.id_serie')
            ->join('tipos_comprobante', 'comprobantes.id_tipo_comprobante', '=', 'tipos_comprobante.id_tipo_comprobante')

            ->select(DB::raw('
                comprobantes.fecha_emision,
                series_inv.serie,
                comprobantes.correlativo,
                clientes.nombre,
                deudas_comprobantes.*,
                tipos_comprobante.tipo_comprobante'));

        if(isset($searchTerm)){
            $datos = $datos->where('clientes.nombre', 'like', "%{$searchTerm}%")
            ->orWhere('clientes.nro_doc', 'like', "%{$searchTerm}%");
        }
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $datos = $datos->whereBetween('comprobantes.fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $datos = $datos->where('comprobantes.fecha_emision', '>=', $fechaInicio);
            }
        }

        return $datos->groupBy('deudas_comprobantes.id_deuda')->paginate($request->perPage);
    }

    public function getCuentasPagar(Request $request){
        $authUser = auth('api')->user();

        $searchTerm = $request->searchTerm;
        $fechaInicio = date($request->fechaInicio);
        $fechaFin    = date($request->fechaFin);

        $datos = DB::table('deudas_compras')
            ->join('compras', 'deudas_compras.id_compra', '=', 'compras.id_compra')
            ->join('proveedores', 'deudas_compras.id_proveedor', '=', 'proveedores.id_proveedor')
            ->where([
                ['compras.id_sucursal', $authUser->id_sucursal]
            ])
            ->select(DB::raw('
                compras.fecha_emision,
                compras.serie_factura,
                compras.nro_factura,
                proveedores.nombre,
                proveedores.nro_doc,
                deudas_compras.*'));

        if(isset($searchTerm)){
            $datos = $datos->where('proveedores.nombre', 'like', "%{$searchTerm}%")
            ->orWhere('proveedores.nro_doc', 'like', "%{$searchTerm}%");
        }
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $datos = $datos->whereBetween('compras.fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $datos = $datos->where('compras.fecha_emision', '>=', $fechaInicio);
            }
        }

        return $datos->orderBy('compras.fecha_emision', 'desc')->paginate($request->perPage);
    }
}
