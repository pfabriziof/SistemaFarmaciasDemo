<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Common\ServicesController;
use App\Http\Controllers\Api\Common\ChatGptController;
use App\Http\Controllers\Api\Common\DocGenerationController;
use App\Http\Controllers\Api\Common\UbigeoController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/auth/checkActiveSession', [LoginController::class, 'checkActiveSession']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResources([
        //--- Ventas ---
        'comprobante' => ComprobanteController::class,
        //--- End ---

        //--- Caja ---
        'caja' => CajaController::class,
        'egresos' => EgresosController::class,
        //--- End ---

        //--- Compras ---
        'compra' => CompraController::class,
        'orden_compra' => OrdenCompraController::class,
        'prv_cotizacion' => ProveedorCotizacionController::class,
        //--- End ---

        'almacen' => AlmacenMovimientosController::class,

        //--- MAESTROS ---
        //--- Productos ---
        'producto' => ProductoController::class,
        'categoria' => CategoryController::class,
        'marca' => MarcaController::class,
        'laboratorio' => LaboratorioController::class,
        'condicion_almacenamiento' => CondicionAlmController::class,
        'unidades_medida' => UnidadesMedidaController::class,
        'price_list' => ListaPreciosController::class,
        'pricelist_detail' => ListaPreciosDetalleController::class,
        //--- End ---
        //--- Empresa ---
        'cliente'     => ClienteController::class,
        'deuda'       => DeudaComprobanteController::class,

        'proveedor'   => ProveedorController::class,
        'deuda_compra'=> DeudaCompraController::class,

        'sucursal'    => SucursalController::class,
        'empresa'     => EmpresaController::class,


        'usuario'    => UsuarioController::class,
        'user_roles' => UserRolesController::class,
        'permissions' => PermissionsController::class,

        'series_invoice' => SeriesInvController::class,
        //--- End ---
    ]);

    //--- DASHBOARD ---
    Route::get('getDashboardHistorico', [DashboardController::class, 'getDashboardHistorico']);
    Route::get('getVentasEgresos', [DashboardController::class, 'getVentasEgresos']);
    Route::get('getTopProductos', [DashboardController::class, 'getTopProductos']);
    //--- END ---



    //--- REPORTES ---
    Route::post('getDigemidObservatorio', [ReporteDigemidController::class, 'getDigemidObservatorio']);
    Route::post('getDigemidListadoPrecios', [ReporteDigemidController::class, 'getDigemidListadoPrecios']);
    Route::get('getKardexValorizado', [KardexController::class, 'getKardexValorizado']);
    //--- END ---



    //--- PROCEDIMIENTOS ---
    //Comprobantes
    Route::post('sendMailComprobante', [DocGenerationController::class, 'sendMailComprobante']);
    Route::get('enviarComprobanteSunat/{id}', [ComprobanteController::class, 'enviarComprobanteSunat']);
    Route::get('seriesComprobanteCombo/{id}', [ServicesController::class, 'seriesComprobanteCombo']);

    //Caja
    Route::get('cajaAbierta', [CajaController::class, 'cajaAbierta']);
    Route::get('getDetalleCaja/{id}', [CajaController::class, 'getDetalleCaja']);
    Route::get('getMontosDelDia', [CajaController::class, 'getMontosDelDia']);

    //Compras
    Route::post('sendMailCompra', [DocGenerationController::class, 'sendMailCompra']);
    Route::post('sendMailCotizacion', [DocGenerationController::class, 'sendMailCotizacion']);
    Route::post('sendMailOrdenCompra', [DocGenerationController::class, 'sendMailOrdenCompra']);


    //Cuentas Cobrar / Pagar
    Route::get('getCuentasPagar', [CuentasController::class, 'getCuentasPagar']);
    Route::get('getCuentasCobrar', [CuentasController::class, 'getCuentasCobrar']);
    //--- END ---


    //--- CATALOGOS ---
    //--- PRODUCTOS ---
    //Listas Precios
    Route::post('getPriceListDetail', [ListaPreciosDetalleController::class, 'getPriceListDetail']);
    Route::get('getListPricebyProduct/{id}', [ListaPreciosDetalleController::class, 'getListPricebyProduct']);
    Route::get('getAllListPriceByProduct/{id}', [ListaPreciosDetalleController::class, 'getAllListPriceByProduct']);
    //--- END ---

    //--- EMPRESA ---
    //Clientes
    Route::get('getClienteDeuda/{id}', [ClienteController::class, 'getClienteDeuda']);
    Route::get('getClienteDirecciones/{id}', [ClienteController::class, 'getClienteDirecciones']);
    Route::get('getClientePredeterminado', [ClienteController::class, 'getClientePredeterminado']);
    //DeudasComprobante
    Route::post('getDeudasCliente', [DeudaComprobanteController::class, 'getDeudasCliente']);
    Route::post('getDeudaPagos', [DeudaComprobanteController::class, 'getDeudaPagos']);
    Route::post('deleteDeudaPago/{id}', [DeudaComprobanteController::class, 'deleteDeudaPago']);

    //Proveedores
    Route::get('getProveedorDeuda/{id}', [ProveedorController::class, 'getProveedorDeuda']);
    //DeudasCompra
    Route::post('getDeudasProveedor', [DeudaCompraController::class, 'getDeudasProveedor']);
    Route::post('getDeudaCompraPagos', [DeudaCompraController::class, 'getDeudaCompraPagos']);
    Route::post('deleteDeudaCompraPago/{id}', [DeudaCompraController::class, 'deleteDeudaCompraPago']);

    //Empresa
    Route::get('getEmpresaUsuarioSesion', [EmpresaController::class, 'getEmpresaUsuarioSesion']);
    //--- END ---
    //--- END ---


    //--- SERVICES ---
    //Roles y Permisos
    Route::get('userRolesCombo', [UserRolesController::class, 'userRolesCombo']);
    Route::post('assignPermissionsToRole/{id}', [PermissionsController::class, 'assignPermissionsToRole']);
    //--- END ---

    //--- Busqueda Nro Doc ---
    Route::post('searchRuc', [ServicesController::class, 'searchRuc']);
    Route::post('searchDni', [ServicesController::class, 'searchDni']);
    //--- End ---

    //--- Live Search ---
    Route::post('buscarProductos', [ServicesController::class, 'buscarProductos']);
    Route::post('buscarProductosCompra', [ServicesController::class, 'buscarProductosCompra']);
    Route::post('buscarProveedores', [ServicesController::class, 'buscarProveedores']);
    Route::post('buscarClientesComprobante/{id_tipo_comp}', [ServicesController::class, 'buscarClientesComprobante']);
    Route::post('buscarLotes/{id}', [ServicesController::class, 'buscarLotes']);
    //--- End ---

    //--- Chat GPT ---
    Route::post('chatgpt/SendQuery', [ChatGptController::class, 'SendQuery']);
    Route::get('compressedTablesCombo', [ChatGptController::class, 'compressedTablesCombo']);
    //--- End ---


    //--- Combo Routes ---
    Route::get('sucursalesCombo', [SucursalController::class, 'sucursalesCombo']);
    Route::get('tiposDocCombo', [ServicesController::class, 'tiposDocCombo']);
    Route::get('tiposComprobantesCombo', [ServicesController::class, 'tiposComprobantesCombo']);
    Route::get('mediosPagoCombo', [ServicesController::class, 'mediosPagoCombo']);
    Route::get('tiposCambioCombo', [ServicesController::class, 'tiposCambioCombo']);
    Route::get('monedasCombo', [ServicesController::class, 'monedasCombo']);
    Route::get('estadosInvoiceCombo', [ServicesController::class, 'estadosInvoiceCombo']);
    Route::get('categoriasCombo', [CategoryController::class, 'categoriasCombo']);
    Route::get('productosTiposCombo', [ServicesController::class, 'productosTiposCombo']);
    Route::get('unidadesMedidaCombo', [UnidadesMedidaController::class, 'unidadesMedidaCombo']);
    Route::get('marcasCombo', [MarcaController::class, 'marcasCombo']);
    Route::get('laboratoriosCombo', [LaboratorioController::class, 'laboratoriosCombo']);
    Route::get('priceListsCombo', [ListaPreciosController::class, 'priceListsCombo']);
    Route::get('condicionesAlmCombo', [CondicionAlmController::class, 'condicionesAlmCombo']);
    Route::get('empresasCombo', [EmpresaController::class, 'empresasCombo']);
    Route::get('tiposEgresosCombo', [ServicesController::class, 'tiposEgresosCombo']);
    Route::get('motivosEgresoCombo', [ServicesController::class, 'motivosEgresoCombo']);
    Route::get('histMotivosIngresoCombo', [ServicesController::class, 'histMotivosIngresoCombo']);
    Route::get('histMotivosSalidaCombo', [ServicesController::class, 'histMotivosSalidaCombo']);
    //--- End ---

    //--- Ubigeo ---
    Route::get('getDepartments', [UbigeoController::class, 'getDepartments']);
    Route::get('getProvinces/{id}', [UbigeoController::class, 'getProvinces']);
    Route::post('getDistricts', [UbigeoController::class, 'getDistricts']);
    //--- End ---
    //--- END ---
});