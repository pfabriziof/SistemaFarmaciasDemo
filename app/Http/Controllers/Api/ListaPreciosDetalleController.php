<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ListaPreciosDetalle;
use App\Models\Producto;
use Exception;

class ListaPreciosDetalleController extends Controller
{
    public function index()
    {
        //
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'precio_compra'   => 'required',
            'precio_venta'    => 'required',
            'id_producto'     => 'required|integer',
            'id_lista_precio' => 'required|integer',
        ]);
        
        $producto = Producto::find($request->id_producto);
        ListaPreciosDetalle::create([
            'id_lista_precio' => $request->id_lista_precio,
            'id_producto'     => $request->id_producto,
            'id_sucursal'     => $producto->id_sucursal,
            'precio_compra'   => $request->precio_compra,
            'precio_venta'    => $request->precio_venta,
        ]);

        return response()->json(['success'=>true, 'message' => 'Detalle de lista de precio creado correctamente!']);
    }
    
    public function show($id)
    {
        try{
            $data = ListaPreciosDetalle::findOrFail($id);
            return response()->json($data, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'precio_compra'   => 'required',
            'precio_venta'   => 'required',
            'id_producto'     => 'required|integer',
            'id_lista_precio' => 'required|integer',
        ]);
        
        $data = ListaPreciosDetalle::findOrFail($id);
        $data->update( $request->all());

        return response()->json(['success'=>true, 'message' => 'Detalle de lista de precio actualizado correctamente!']);
    }
    
    public function destroy($id)
    {
        try {
            $data = ListaPreciosDetalle::findOrFail($id);
            $data->estado = $data->estado != 1? 1 : 0;
            $data->save();

            return response()->json(["success"=>true], 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getPriceListDetail(Request $request) {
        $authUser = auth('api')->user();
        $search_query = $request->data["search_query"];
        $data = ListaPreciosDetalle::where([
            ['id_sucursal', $authUser->id_sucursal],
            ['id_lista_precio', $request->id_lista],
        ]);

        if($search_query != null){
            $data = $data->whereHas('producto', function ($query) use ($search_query) {
                $query->where('nombreProducto', 'like', "%{$search_query}%")
                      ->orWhere('codigo_producto', 'like', "%{$search_query}%");
            });
        }

        return $data->orderBy('id_producto', 'DESC')->paginate($request->per_page);
    }

    public function getListPricebyProduct($id){
        $authUser = auth('api')->user();
        $data = ListaPreciosDetalle::where([
            ['id_sucursal', $authUser->id_sucursal],
            ['id_producto', $id],
            ['estado', 1]
        ])->get();
        return response()->json($data, 200);
    }

    public function getAllListPriceByProduct($id){
        $authUser = auth('api')->user();
        $data = ListaPreciosDetalle::where([
            ['id_sucursal', $authUser->id_sucursal],
            ['id_producto', $id],
        ])->get();
        return response()->json($data, 200);
    }
}
