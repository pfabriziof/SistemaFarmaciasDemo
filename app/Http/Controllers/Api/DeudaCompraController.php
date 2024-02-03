<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeudaCompra;
use App\Models\DeudaCompraPago;
use Illuminate\Http\Request;

class DeudaCompraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'monto_pago' => 'required',
            'fecha_pago'   => 'required',
        ]);

        $deuda = DeudaCompra::findOrFail($request->id_deuda);
        if($request->monto_pago > $deuda->total_monto_pendiente){
            return response()->json(['errors' => ['monto_excedido' => ['Monto de '.$request->monto_pago.' mayor a deuda pendiente']]], 500);
        }
        
        $deuda->total_monto_pendiente = (float)$deuda->total_monto_pendiente - (float)$request->monto_pago;
        $deuda->total_monto_pagado    = (float)$deuda->total_monto_pagado + (float)$request->monto_pago;
        $deuda->save();

        return DeudaCompraPago::create([
            'id_deuda'     => $request->id_deuda,
            'monto_pagado' => $request->monto_pago,
            'comentario'   => $request->comentario,
            'fecha'        => $request->fecha_pago,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getDeudasProveedor(Request $request) {
        $fecha_inicio = $request->data["fecha_inicio"];
        $fecha_fin    = $request->data["fecha_fin"];
        $datos = DeudaCompra::where([
            ["id_proveedor", $request->data["id_proveedor"]],
            ["estado", 1],
        ]);

        if($fecha_inicio != null){
            if($fecha_fin != null){
                $datos = $datos->whereBetween('fecha', [$fecha_inicio, $fecha_fin]);
            }else{
                $datos = $datos->where('fecha', '>=', $fecha_inicio);
            }
        }

        return $datos->paginate($request->per_page);
    }

    public function getDeudaCompraPagos(Request $request){
        $datos = DeudaCompraPago::where([
            ["id_deuda", $request->id_deuda],
            ['estado', 1]
        ])->orderBy('fecha','desc')->paginate(10);
        return $datos;
    }

    public function deleteDeudaCompraPago($id){
        $deuda_pago = DeudaCompraPago::findOrFail($id);
        $deuda = DeudaCompra::findOrFail($deuda_pago->id_deuda);
        
        $deuda->total_monto_pendiente = (float)$deuda->total_monto_pendiente + (float)$deuda_pago->monto_pagado;
        $deuda->total_monto_pagado    = (float)$deuda->total_monto_pagado - (float)$deuda_pago->monto_pagado;
        $deuda->save();
        
        $deuda_pago->estado = 0;
        $deuda_pago->save();

        return ['message' => 'Pago de deuda eliminada.'];
    }
}
