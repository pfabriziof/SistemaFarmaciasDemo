<?php

namespace App\Http\Controllers\Api\Common;

use App\Models\AlmacenMovimiento;
use App\Models\Compra;
use App\Models\Comprobante;
use App\Http\Controllers\Controller;

use App\Exports\Reports\CuentasCobrarExport;
use App\Exports\Reports\CuentasPagarExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LengthException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ExportsController extends Controller
{
    public function exportarKardexValorizado($params, Xlsx $writer){
        // $startTime = microtime(true); // Capture start time

        $authUser = auth()->user();

        $params = json_decode($params);
        $searchTerm = $params->searchTerm;
        $fechaInicio = $params->fechaInicio;
        $fechaFin    = $params->fechaFin;

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
        $period_string = '';
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $period_string = date("Y/m", strtotime($fechaInicio)).' - '.date("Y/m", strtotime($fechaFin));
                $datos = $datos->whereBetween('comprobantes.fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $period_string = date("Y/m", strtotime($fechaInicio));
                $datos = $datos->where('comprobantes.fecha_emision', '>=', $fechaInicio);
            }
        }
        $datos = $datos->groupBy('productos_servicios.id_producto')->get();


        //--- CREACION DE HOJA EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Hoja1");

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);

        $sheet->getStyle('E')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('F')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('G')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('H')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('I')->getNumberFormat()->setFormatCode('0.00');


        //--- Cabecera Doc. ---
        $sheet->mergeCells('A1:AD1');
        $sheet->mergeCells('A2:AD2');
        $sheet->mergeCells('A3:AD3');
        
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('left');
        $sheet->setCellValue('A1', $authUser->sucursal->empresa->nombre);
        $sheet->setCellValue('A3', 'REPORTE KÁRDEX VALORIZADO DEL PERIODO '.$period_string);
        //--- End ---

        //--- Cabecera Tabla ---
        $sheet->setCellValue('A4', 'PRODUCTO');
        $sheet->setCellValue('B4', 'CATEGORÍA');
        $sheet->setCellValue('C4', 'MARCA');
        $sheet->setCellValue('D4', 'UNIDAD');
        $sheet->setCellValue('E4', 'UNIDADES FÍSICAS VENDIDAS');
        $sheet->setCellValue('F4', 'COSTO UNITARIO');
        $sheet->setCellValue('G4', 'VALOR DE VENTAS');
        $sheet->setCellValue('H4', 'COSTO DE PRODUCTO');
        $sheet->setCellValue('I4', 'UNIDAD VALORIZADA');
        //--- End ---

        //--- Contenido de la Tabla ---
        $sprst_row = 5;
        foreach($datos as $key=>$dt){
            $sheet->setCellValue('A'.$sprst_row, $dt->nombreProducto);
            $sheet->setCellValue('B'.$sprst_row, $dt->categoria);
            $sheet->setCellValue('C'.$sprst_row, $dt->marca);
            $sheet->setCellValue('D'.$sprst_row, $dt->unidad_medida);
            $sheet->setCellValue('E'.$sprst_row, $dt->und_vendido);
            $sheet->setCellValue('F'.$sprst_row, $dt->costo_unitario);
            $sheet->setCellValue('G'.$sprst_row, $dt->valor_ventas);
            $sheet->setCellValue('H'.$sprst_row, $dt->costo_producto);
            $sheet->setCellValue('I'.$sprst_row, $dt->unidad_valorizada);
            $sprst_row+=1;
        }
        //--- End ---
        
        $writer->setSpreadsheet($spreadsheet);
        $file_name = "Reporte_Kardex_Valorizado_".time().".xlsx";
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        $writer->save('php://output');
        //--- END --- 

        // $endTime = microtime(true); // Capture end time
        // $executionTime = $endTime - $startTime;
        // Log::debug("Kardex Xlsx: Tiempo de ejecución: " . $executionTime . " segs");
    }
    
    public function exportarReporteAlmacen($params, Xlsx $writer){
        // $startTime = microtime(true); // Capture start time

        $authUser = auth()->user();

        $params = json_decode($params);
        $searchTerm = $params->searchTerm;
        $fechaInicio = $params->fechaInicio;
        $fechaFin    = $params->fechaFin;

        $datos = AlmacenMovimiento::where([
            ['id_sucursal', $authUser->id_sucursal],
        ]);

        if(isset($searchTerm)){
            $datos = $datos->where(function ($query) use ($searchTerm) {
                $query->where('nombreProducto', 'like', "%{$searchTerm}%");
            });
        }
        $period_string = '';
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $period_string = date("Y/m", strtotime($fechaInicio)).' - '.date("Y/m", strtotime($fechaFin));
                $datos = $datos->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);
            }else{
                $period_string = date("Y/m", strtotime($fechaInicio));
                $datos = $datos->where('fecha_movimiento', '>=', $fechaInicio);
            }
        }
        $datos = $datos->get();

        //--- CREACION DE HOJA EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Hoja1");
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);

        $sheet->getStyle('F')->getAlignment()->setHorizontal('center');

        
        //--- Cabecera Doc. ---
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('left');
        $sheet->setCellValue('A1', 'REPORTE MOVIMIENTOS DE ALMACEN DEL PERIODO'.$period_string);
        //--- End ---

        //--- Cabecera Tabla ---
        $sheet->setCellValue('A3', 'TIPO MOVIMIENTO');
        $sheet->setCellValue('B3', 'PRODUCTO');
        $sheet->setCellValue('C3', 'LABORATORIO');
        $sheet->setCellValue('D3', 'FECHA');
        $sheet->setCellValue('E3', 'CANTIDAD');
        $sheet->setCellValue('F3', 'UNIDAD DE MEDIDA');
        //--- End ---

        //--- Contenido de la Tabla ---
        $sprst_row = 4;
        foreach($datos as $key=>$dt){
            $sheet->setCellValue('A'.$sprst_row, $dt->tipo_movimiento->tipo_movimiento);
            $sheet->setCellValue('B'.$sprst_row, $dt->NombreProducto);
            $sheet->setCellValue('C'.$sprst_row, $dt->producto->laboratorio->nombre);
            $sheet->setCellValue('D'.$sprst_row, $dt->fecha_movimiento);
            $sheet->setCellValue('E'.$sprst_row, $dt->cantidad);
            $sheet->setCellValue('F'.$sprst_row, $dt->und_simbolo);

            $sprst_row+=1;
        }
        //--- End ---

        $writer->setSpreadsheet($spreadsheet);
        $file_name = "Reporte_Movimientos_Almacen".time().".xlsx";
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        $writer->save('php://output');
        //--- END ---

        // $endTime = microtime(true); // Capture end time
        // $executionTime = $endTime - $startTime;
        // Log::debug("Reporte Almacen Xlsx: Tiempo de ejecución: " . $executionTime . " segs");
    }

    public function exportarReporteCompraFormat($params, Xlsx $writer){
        $authUser = auth()->user();
        $params = json_decode($params);
        
        $searchTerm = $params->searchTerm;
        $fechaInicio = $params->fechaInicio;
        $fechaFin    = $params->fechaFin;

        $datos = Compra::where([
            ["id_sucursal", $authUser->id_sucursal],
            ["id_estado", 1],
        ]);

        if(isset($searchTerm)){
            $datos = $datos->where(function ($query) use ($searchTerm) {
                $query->where('nombreProveedor', 'like', "%{$searchTerm}%")
                      ->orWhere('nroDocProveedor', 'like', "%{$searchTerm}%");
            });
        }
        $period_string = '';
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $period_string = date("Y/m", strtotime($fechaInicio)).' - '.date("Y/m", strtotime($fechaFin));
                $datos = $datos->whereBetween('fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $period_string = date("Y/m", strtotime($fechaInicio));
                $datos = $datos->where('fecha_emision', '>=', $fechaInicio);
            }
        }
        $datos = $datos->get();


        //--- CREACION DE HOJA EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Hoja1");

        // $sheet->getColumnDimension('A')->setWidth(15);
        // $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(65);

        //--- Cabecera Doc. ---
        $sheet->mergeCells('A1:AD1');
        $sheet->mergeCells('A2:AD2');
        $sheet->mergeCells('A3:AD3');
        
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('left');
        $sheet->setCellValue('A1', $authUser->sucursal->empresa->nombre);
        $sheet->setCellValue('A3', 'FORMATO 14.1 : "REGISTRO DE COMPRAS DEL PERIODO '.$period_string);
        //--- End ---

        //--- Cabecera Tabla ---
        $sheet->mergeCells('A4:B4');
        $sheet->mergeCells('E4:G4');
        $sheet->mergeCells('I4:K4');
        $sheet->mergeCells('L4:M4');
        $sheet->mergeCells('N4:O4');
        $sheet->mergeCells('P4:Q4');
        $sheet->mergeCells('X4:Y4');
        $sheet->mergeCells('AA4:AD4');

        $sheet->setCellValue('A4', 'NUMERO CORRELATIVO DEL REGISTRO O CUO.');
        $sheet->setCellValue('C4', 'FECHA DE EMISION DEL COMPROBANTE DE PAGO O EMISION DEL DOCUMENTO');
        $sheet->setCellValue('D4', 'FECHA VENC.');
        $sheet->setCellValue('E4', 'COMPROBANTE DE PAGO O DOCUMENTO');
            $sheet->setCellValue('E5', 'TIPO');
            $sheet->setCellValue('F5', 'SERIE');
            $sheet->setCellValue('G5', 'AÑO DE EMISION DE DUA');

        $sheet->setCellValue('H4', 'NRO. DEL COMPROBANTE DE PAGO O DOCUMENTO');
        $sheet->setCellValue('I4', 'INFORMACION DE PROVEEDOR');
            $sheet->setCellValue('I5', 'TIPO');
            $sheet->setCellValue('J5', 'NÚMERO');
            $sheet->setCellValue('K5', 'APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN SOCIAL');

        $sheet->setCellValue('L4', 'ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES GRAVADAS Y/O EXPORTACION');
            $sheet->setCellValue('L5', 'BASE IMPONIBLE');
            $sheet->setCellValue('M5', 'IGV');

        $sheet->setCellValue('N4', 'ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES GRAVADAS Y/O EXPORTACION Y A OPERACIONES NO GRAVADAS');
            $sheet->setCellValue('N5', 'BASE IMPONIBLE');
            $sheet->setCellValue('O5', 'IGV');

        $sheet->setCellValue('P4', 'ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES NO GRAVADAS');
            $sheet->setCellValue('P5', 'BASE IMPONIBLE');
            $sheet->setCellValue('Q5', 'IGV');

        $sheet->setCellValue('R4', 'VALOR DE LAS ADQUISICIONES NO GRAVADAS');
        $sheet->setCellValue('S4', 'ISC');
        $sheet->setCellValue('T4', 'OTROS TRIBUTOS Y CARGOS');
        $sheet->setCellValue('U4', 'NUMERO DE COMPROBANTE DE PAGO EMITIDO POR SUJETO NO DOMICILIADO');
        $sheet->setCellValue('V4', 'IMPORTE TOTAL');
        $sheet->setCellValue('W4', 'MONEDA');
        $sheet->setCellValue('X4', 'CONSTANCIA DE DEPOSITO DE DETRACCION');
            $sheet->setCellValue('X5', 'NÚMERO');
            $sheet->setCellValue('Y5', 'FECHA DE EMISIÓN');

        $sheet->setCellValue('Z4', 'TIPO DE CAMBIO');
        $sheet->setCellValue('AA4', 'REFERENCIA DEL COMPROBANTE DE PAGO O DOCUMENTO ORIGINAL QUE SE MODIFICA');
            $sheet->setCellValue('AA5', 'FECHA');
            $sheet->setCellValue('AB5', 'TIPO');
            $sheet->setCellValue('AC5', 'SERIE');
            $sheet->setCellValue('AD5', 'NÚMERO');
        //--- End ---

        //--- Contenido de la Tabla ---
        $sprst_row = 6;
        foreach($datos as $key=>$dt){
            $sheet->setCellValue('A'.$sprst_row, '05');
            $sheet->setCellValue('B'.$sprst_row, $dt->correlativo);
            $sheet->setCellValue('C'.$sprst_row, date("d/m/Y", strtotime($dt->fecha_emision)));
            $sheet->setCellValue('D'.$sprst_row, date("d/m/Y", strtotime($dt->fecha_vencimiento)));

            $sheet->setCellValue('E'.$sprst_row, $dt->tipo_comprobante->codigo_sunat);
            $sheet->setCellValue('F'.$sprst_row, $dt->serie_factura);
            // $sheet->setCellValue('G'.$sprst_row, $dt->); // En Blanco
            $sheet->setCellValue('H'.$sprst_row, $dt->nro_factura);

            $sheet->setCellValue('I'.$sprst_row, $dt->proveedor->tipo_doc->codigo_sunat);
            $sheet->setCellValue('J'.$sprst_row, $dt->nroDocProveedor);
            $sheet->setCellValue('K'.$sprst_row, $dt->nombreProveedor);

            // $sheet->setCellValue('L'.$sprst_row, $dt->); // En Blanco
            // $sheet->setCellValue('M'.$sprst_row, $dt->); // En Blanco
            $sheet->setCellValue('N'.$sprst_row, $dt->op_gravadas);
            $sheet->setCellValue('O'.$sprst_row, $dt->igv);
            // $sheet->setCellValue('P'.$sprst_row, $dt->); // En Blanco
            // $sheet->setCellValue('Q'.$sprst_row, $dt->); // En Blanco
            $adqs_nogravadas = $dt->op_inafectas + $dt->op_exoneradas;
            $sheet->setCellValue('R'.$sprst_row, $adqs_nogravadas);
            $sheet->setCellValue('S'.$sprst_row, $dt->icbper);

            // $sheet->setCellValue('T'.$sprst_row, $dt->fecha_emision); // En Blanco
            // $sheet->setCellValue('U'.$sprst_row, $dt->fecha_emision); // En Blanco
            $sheet->setCellValue('V'.$sprst_row, $dt->total);
            $sheet->setCellValue('W'.$sprst_row, $dt->moneda->simbolo);
            // $sheet->setCellValue('X'.$sprst_row, $dt->); // En Blanco
            // $sheet->setCellValue('Y'.$sprst_row, $dt->); // En Blanco
            $sheet->setCellValue('Z'.$sprst_row, $dt->tipo_cambio->cambio);
        
            // $sheet->setCellValue('AA'.$sprst_row, $dt->); // En Blanco
            // $sheet->setCellValue('AB'.$sprst_row, $dt->); // En Blanco
            // $sheet->setCellValue('AC'.$sprst_row, $dt->); // En Blanco
            // $sheet->setCellValue('AD'.$sprst_row, $dt->); // En Blanco

            $sprst_row+=1;
        }
        //--- End ---  

        $writer->setSpreadsheet($spreadsheet);
        $file_name = "Reporte_Formato_Compras_141_".time().".xlsx";
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        $writer->save('php://output');
        //--- END ---
    }

    public function exportarReporteComprobanteGeneral($params, Xlsx $writer){
        // $startTime = microtime(true); // Capture start time

        $authUser = auth()->user();

        $params = json_decode($params);
        $searchTerm = $params->searchTerm;
        $fechaInicio = $params->fechaInicio;
        $fechaFin = $params->fechaFin;
        $tipo_doc = $params->id_tipo_comprobante;
        $id_estado = $params->id_estado;

        $datos = Comprobante::where([
            ["id_sucursal", $authUser->id_sucursal],
        ]);

        if(isset($searchTerm)){
            $datos = $datos->where(function ($query) use ($searchTerm) {
                $query->where('nombreCliente', 'like', "%{$searchTerm}%")
                      ->orWhere('nroDocCliente', 'like', "%{$searchTerm}%");
            });
        }
        $period_string = '';
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $period_string = date("Y/m", strtotime($fechaInicio)).' - '.date("Y/m", strtotime($fechaFin));
                $datos = $datos->whereBetween('fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $period_string = date("Y/m", strtotime($fechaInicio));
                $datos = $datos->where('fecha_emision', '>=', $fechaInicio);
            }
        }
        if(isset($tipo_doc)){
            $datos = $datos->where('id_tipo_comprobante', $tipo_doc);
        }
        if(isset($id_estado)){
            $datos = $datos->where('id_estado_comprobante', $id_estado);
        }
        $datos = $datos->get();


        //--- CREACION DE HOJA EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Hoja1");
        
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        
        $sheet->getStyle('F')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('G')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('H')->getNumberFormat()->setFormatCode('0.00');


        //--- Cabecera Doc. ---
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');
        
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('A2:I2')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('A3:I3')->getAlignment()->setHorizontal('left');
        $sheet->setCellValue('A1', $authUser->sucursal->empresa->nombre);
        $sheet->setCellValue('A3', 'REPORTE GENERAL DE COMPROBANTES DEL PERIODO '.$period_string);
        //--- End ---

        //--- Cabecera Tabla ---
        $sheet->setCellValue('A4', 'TIPO DOCUMENTO');
        $sheet->setCellValue('B4', 'NÚMERO');
        $sheet->setCellValue('C4', 'FECHA DE EMISION');
        $sheet->setCellValue('D4', 'CLIENTE');
        $sheet->setCellValue('E4', 'DNI/RUC');
        $sheet->setCellValue('F5', 'GRAVADO');
        $sheet->setCellValue('G5', 'IGV');
        $sheet->setCellValue('H4', 'TOTAL');
        // $sheet->setCellValue('I4', 'ESTADO');
        //--- End ---

        //--- Contenido de la Tabla ---
        $sprst_row = 6;
        foreach($datos as $key=>$dt){
            $sheet->setCellValue('A'.$sprst_row, $dt->tipo_comprobante->tipo_comprobante);
            $sheet->setCellValue('B'.$sprst_row, $dt->serie->serie.' - '.str_pad($dt->correlativo, 8, '0', STR_PAD_LEFT));
            $sheet->setCellValue('C'.$sprst_row, $dt->fecha_emision);
            $sheet->setCellValue('D'.$sprst_row, $dt->nombreCliente);
            $sheet->setCellValue('E'.$sprst_row, $dt->nroDocCliente);
            $sheet->setCellValue('F'.$sprst_row, $dt->op_gravadas);
            $sheet->setCellValue('G'.$sprst_row, $dt->igv);
            $sheet->setCellValue('H'.$sprst_row, $dt->total);
            // $sheet->setCellValue('I'.$sprst_row, $dt->estado->estado);

            $sprst_row+=1;
        }
        //--- End ---

        $writer->setSpreadsheet($spreadsheet);
        $file_name = "Reporte_Ventas_General".time().".xlsx";
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        $writer->save('php://output');
        //--- END ---

        // $endTime = microtime(true); // Capture end time
        // $executionTime = $endTime - $startTime;
        // Log::debug("Reporte Comprobantes Xlsx: Tiempo de ejecución: " . $executionTime . " segs");
    }

    //--- Cuentas ---
    public function exportarCuentasCobrar($params, Excel $excel){
        $params_json = json_decode($params, true);
        return $excel::download(new CuentasCobrarExport($params_json), 'ReporteCuentasCobrar_'.time().'.xlsx');
    }
    public function exportarCuentasPagar($params, Excel $excel){
        $params_json = json_decode($params, true);
        return $excel::download(new CuentasPagarExport($params_json), 'ReporteCuentasPagar_'.time().'.xlsx');
    }
    //--- End ---
}
