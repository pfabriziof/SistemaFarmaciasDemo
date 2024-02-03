<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardHistoric(){
        $data = DB::select('call prd_ventas()');
        return $data;
        //$data = DB::select('call prd_ventas(?, ?)',array($parametro1, $parametro 2));
    }

    public function getDashboardToday(){
        $data = DB::select('call prd_ventas_today()');
        return $data;
    }
    
    public function getDashboardStock(){
        $data = DB::table('productos_servicios')
        ->select('codigo_producto','nombreProducto','stock','stock_minimo')
        ->orderBy('stock')
        ->orderBy('stock_minimo')
        ->get();
        return $data;
    }
}
