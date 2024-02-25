<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardHistorico(){
        $data = DB::select('call prd_ventas()');
        return $data;
    }

    public function getVentasEgresos(){
        $data = DB::select('call prd_ventas_today()');
        return $data;
    }
    
    public function getTopProductos(Request $request){
        $data = DB::table('comprobante_detalle')
        ->select('nombre_producto', DB::raw('SUM(cantidad) AS cnt'))
        // ->where()
        ->groupBy('nombre_producto')
        ->orderByRaw('cnt DESC')
        ->take(5)
        ->get();

        return $data;
    }
}
