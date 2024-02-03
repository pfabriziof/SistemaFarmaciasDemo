<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Common\ExportsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    // 'verify' => false, // Email Verification Routes...
]);
Route::post('/login', [LoginController::class, 'authenticate']);


Route::post('files/upload-company-file', [FilesUploadingController::class, 'uploadCompanyFile']);
//--- End ---
//--- Compras ---
Route::get('/generarProveedorCotizacionPDF/{id}', [ProveedorCotizacionController::class, 'generarProveedorCotizacionPDF'])->middleware('auth');
Route::get('/generarOrdenCompraPDF/{id}', [OrdenCompraController::class, 'generarOrdenCompraPDF'])->middleware('auth');
Route::get('/generarCompraPDF/{id}', [CompraController::class, 'generarCompraPDF'])->middleware('auth');
//--- End ---
//--- Caja ---
Route::get('/generarCajaPDF/{id}', [CajaController::class, 'generarCajaPDF'])->middleware('auth');
//--- End ---

//--- REPORTES ---
//--- Exports Controller ---
Route::get('/exportarKardexValorizado/{data}', [ExportsController::class, 'exportarKardexValorizado'])->middleware('auth');
Route::get('/exportarReporteAlmacen/{data}', [ExportsController::class, 'exportarReporteAlmacen'])->middleware('auth');
Route::get('/exportarReporteComprobanteGeneral/{data}', [ExportsController::class, 'exportarReporteComprobanteGeneral'])->middleware('auth');
Route::get('/exportarReporteCompraFormat/{data}', [ExportsController::class, 'exportarReporteCompraFormat'])->middleware('auth');

Route::get('/exportarCuentasPagar/{data}', [ExportsController::class, 'exportarCuentasPagar'])->middleware('auth');
Route::get('/exportarCuentasCobrar/{data}', [ExportsController::class, 'exportarCuentasCobrar'])->middleware('auth');
//--- End ---

Route::get('/exportarReporteListaPrecios/{id_sucursal}', [ReporteDigemidController::class, 'exportarReporteListaPrecios'])->middleware('auth');
Route::get('/exportarReporteObservatorio/{id_sucursal}', [ReporteDigemidController::class, 'exportarReporteObservatorio'])->middleware('auth');
//--- END ---


Route::get('files/{path_file}/{file}', function($path_file = null, $file = null) {
    $path = storage_path().'/files/uploads/'.$path_file.'/'.$file;
    return $path;
});

Route::get('/{any}', [HomeController::class, 'index'])->name('home')->where('any', '.*');