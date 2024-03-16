<?php

namespace App\Http\Controllers\Api\Common;

use App\Models\Cliente;
use App\Models\EgresoMotivo;
use App\Http\Controllers\Controller;
use App\Models\MedioPago;
use App\Models\Moneda;
use App\Models\TipoComprobante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\TipoDocumento;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\SerieInv;
use App\Models\ComprobanteEstado;
use App\Models\CustomRole;
use App\Models\LoteProducto;
use App\Models\ProductoTipo;
use App\Models\TipoCambio;
use App\Models\TipoEgreso;
use Exception;
use Illuminate\Support\Facades\DB;

class ServicesController extends Controller
{
    //Retorna todos los tipos de documentos (DNI, RUC, ETC...)
    public function tiposDocCombo() {
        return response()->json(TipoDocumento::all());
    }

    //Retorna los tipos de comprobantes (Boleta, Factura, Proforma...)
    public function tiposComprobantesCombo() {
        return response()->json(TipoComprobante::all());
    }

    //--- Tipo Cambio Functions ---
    public function tiposCambioCombo() {
        return response()->json(TipoCambio::all());
    }
    public function updateTipoCambio(){
        $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://www.sunat.gob.pe/a/txt/tipoCambio.txt',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);
            $response = explode('|', $response);
            // dd($response [2]);

            $cambio_usd = TipoCambio::find(1);
            $cambio_usd->cambio = (float) $response[2];
            $cambio_usd->save();

        curl_close($curl);
    }
    //--- End ---

    public function mediosPagoCombo() {
        return response()->json(MedioPago::all());
    }
    public function monedasCombo() {
        return response()->json(Moneda::all());
    }
    public function productosTiposCombo(){
        return response()->json(ProductoTipo::all());
    }
    public function tiposEgresosCombo(){
        return response()->json(TipoEgreso::all());
    }
    public function motivosEgresoCombo(){
        return response()->json(EgresoMotivo::all());
    }
    
    public function seriesComprobanteCombo($id_tipocomp) {
        $user = auth('api')->user();
        $data = SerieInv::where([
            ['id_sucursal', $user->id_sucursal],
            ['id_tipo_comprobante',$id_tipocomp],
        ])->get();

        return response()->json($data);
    }
    public function estadosInvoiceCombo() {
        return response()->json(ComprobanteEstado::all());
    }


    public function searchRuc(Request $request){
        $url_consulta = config('global.url_api_ruc');
        $token_consulta = config('global.token_api_ruc');

        $response = Http::get($url_consulta.$request->ruc.$token_consulta);

        return $response->json();
    }
    public function searchDni(Request $request){
        $url_consulta = config('global.url_api_dni');
        $token_consulta = config('global.token_api_dni');
        
        $response = Http::get($url_consulta.$request->dni.$token_consulta);

        return $response->json();
    }

    
    //--- AUTOCOMPLETE FUNCTIONS---
    //--- Buscar Clientes ---
    public function buscarClientesComprobante($id_tipo_comp, Request $request){
        $authUser = auth('api')->user();
        $search_query = $request->keywords;
        $data = Cliente::where([
            ["id_sucursal", $authUser->id_sucursal],
            ["estado", 1],
        ]);
        
        $data = $data->where(function ($query) use ($search_query) {
            $query->where('nombre', 'like', "%{$search_query}%")
                  ->orWhere('nro_doc', 'like', "%{$search_query}%");
        });

        if($id_tipo_comp != 99){
            $data = $data->where('id_tipo_doc', $id_tipo_comp);
        }
        
        return response()->json($data->get());
    }
    //--- End ---
    
    public function buscarProveedores(Request $request){
        $authUser = auth('api')->user();
        $search_query = $request->keywords;
        $data = Proveedor::where([
            ["id_sucursal", $authUser->id_sucursal],
            ["estado", 1],
        ]);

        $data = $data->where(function ($query) use ($search_query) {
            $query->where('nombre', 'like', "%{$search_query}%")
                  ->orWhere('nro_doc', 'like', "%{$search_query}%");
        })
        ->get();

        return response()->json($data);
    }
    public function buscarProductos(Request $request){
        $authUser = auth('api')->user();
        $search_query = $request->keywords;
        $data = Producto::where([
            ["id_sucursal", $authUser->id_sucursal],
            ["estado", 1],
        ]);

        $data = $data->where(function ($query) use ($search_query) {
            $query->where('nombreProducto', 'like', "%{$search_query}%")
                  ->orWhere('codigo_producto', 'like', "%{$search_query}%");
        })
        ->get();
        
        return response()->json($data);
    }
    public function buscarProductosCompra(Request $request){
        $authUser = auth('api')->user();
        $search_query = $request->keywords;
        $data = Producto::where([
            ["id_sucursal", $authUser->id_sucursal],
            ["estado", 1],
            ["servicio", 0],
        ]);

        $data = $data->where(function ($query) use ($search_query) {
            $query->where('nombreProducto', 'like', "%{$search_query}%")
                  ->orWhere('codigo_producto', 'like', "%{$search_query}%");
        })
        ->get();
        
        return response()->json($data);
    }
    public function buscarLotes($id_producto, Request $request){
        $authUser = auth('api')->user();
        $productos = LoteProducto::where([
            ['id_sucursal', $authUser->id_sucursal],
            ['id_producto', $id_producto],
            ['lote', 'like', '%'.$request->keywords.'%'],
        ])->get();
        
        return response()->json($productos);
    }
    //--- END ---
}