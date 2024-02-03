<?php

namespace App\Http\Controllers\Api;

use App\Exports\Reports\ListaPreciosExport;
use App\Http\Controllers\Controller;
use App\Models\ListaPreciosDetalle;
use App\Models\Producto;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteDigemidController extends Controller
{
    //--- REPORTE OBSERVATORIO ---
    public function getDigemidObservatorio(Request $request){
        $products = Producto::where([
            ["estado", 1],
        ]);
        $id_sucursal = $request->data["id_sucursal"];
        if($id_sucursal){
            $products = $products->where('id_sucursal', $id_sucursal);
        }
        $products = $products->get();

        $data = array();
        foreach($products as $row){
            $lista_und  = ListaPreciosDetalle::where([
                ['id_lista_precio', 1], // NIU
                ['id_producto', $row->id_producto],
                ['estado', 1],
            ])->first();
            $lista_caja = ListaPreciosDetalle::where([
                ['id_lista_precio', 2], // BX
                ['id_producto', $row->id_producto],
                ['estado', 1],
            ])->first();

            $data[] = array(
                "id_producto"     => $row->id_producto,
                "codigo_producto" => $row->codigo_producto,
                "fecha"           => date('F d Y g:i A'),
                "nombre_producto" => $row->nombreProducto,
                "precio_emp"      => $lista_caja != null? $lista_caja->precio_venta : '',
                "precio_und"      => $lista_und != null? $lista_und->precio_venta : '',
            );
        }
        
        return response()->json(['data'=> $data]);
    }
    public function exportarReporteObservatorio($id_sucursal) {
        $products = Producto::where([
            ["estado", 1],
        ]);
        if($id_sucursal){
            $sucursal = Sucursal::find($id_sucursal);
            $products = $products->where('id_sucursal', $id_sucursal);
        }
        $products = $products->get();

        $data_arr = array();
        foreach($products as $row){
            $lista_und  = ListaPreciosDetalle::where([
                ['id_lista_precio', 1], // NIU
                ['id_producto', $row->id_producto],
                ['id_sucursal', $id_sucursal],
                ['estado', 1],
            ])->first();
            $lista_caja = ListaPreciosDetalle::where([
                ['id_lista_precio', 2], // BX
                ['id_producto', $row->id_producto],
                ['id_sucursal', $id_sucursal],
                ['estado', 1],
            ])->first();
            
            $data_arr[] = array(
                "id_producto"     => $row->id_producto,
                "codigo_producto" => $row->codigo_producto,
                "fecha"           => date('F d Y g:i A'),
                "nombre_producto" => $row->nombreProducto,
                "precio_emp"      => $lista_caja != null? $lista_caja->precio_venta : '',
                "precio_und"      => $lista_und != null? $lista_und->precio_venta : '',
            );
        }
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Hoja1");
        
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);

        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A1:F1')->getFont()->setBold( true )
                                 ->getColor()->setARGB('003362');
        $sheet->getStyle('A2:F2')->getFont()->setBold( true )
                                 ->getColor()->setARGB('003362');
        
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('center');
        $sheet->setCellValue('A1', 'LISTA DE PRECIOS DE MEDICAMENTOS');
        $sheet->setCellValue('A2', 'Observatorio de Productos Farmacéuticos - MINSA');
        
        $sheet->getStyle("A4:A6")->getFont()->setBold( true );
        $sheet->setCellValue('A4', 'Establec.:');
        $sheet->setCellValue('A5', 'Dirección:');
        $sheet->setCellValue('A6', 'Periodo:');

        $sheet->getStyle("B8:F8")->getFont()->setBold( true );
        $sheet->setCellValue('B8', 'CodPro');
        $sheet->setCellValue('C8', 'Fecha');
        $sheet->setCellValue('D8', 'Producto');
        $sheet->setCellValue('E8', 'Prec. Empaque S/.');
        $sheet->setCellValue('F8', 'Prec. Unitario S/.');

        $sheet->getStyle('E')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('F')->getNumberFormat()->setFormatCode('0.00');

        if($id_sucursal){
            $sheet->setCellValue('B4', $sucursal->nombre_sucursal);
            $sheet->setCellValue('B5', $sucursal->direccion_fiscal);
        }else{
            $sheet->setCellValue('B4', "TODOS");
        }
        
        $sheet->setCellValue('B6', date('F d Y'));

        $sprst_row = 9;
        foreach($data_arr as $key=>$data){
            $sheet->setCellValue('A'.$sprst_row, $key + 1);
            $sheet->setCellValue('B'.$sprst_row, $data["codigo_producto"]);
            $sheet->setCellValue('C'.$sprst_row, $data["fecha"]);
            $sheet->setCellValue('D'.$sprst_row, $data["nombre_producto"]);
            if($data["precio_emp"] != ''){
                $sheet->setCellValue('E'.$sprst_row, number_format($data["precio_emp"], 2));
            }
            if($data["precio_und"] != ''){
                $sheet->setCellValue('F'.$sprst_row, number_format($data["precio_und"], 2));
            }
            
            $sprst_row+=1;
        }

        $writer = new Xlsx($spreadsheet);
        $file_name = "ReporteListaPrecios_".time().".xlsx";
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        $writer->save('php://output');
    }
    //--- END ---

    
    //--- REPORTE LISTA PRODUCTOS ---
    public function getDigemidListadoPrecios(Request $request){
        $products = Producto::where([
            ["estado", 1],
        ]);
        $id_sucursal = $request->data["id_sucursal"];
        if($id_sucursal){
            $products = $products->where('id_sucursal', $id_sucursal);
        }
        $products = $products->get();
        
        
        $data = array();
        foreach($products as $row){
            $price_lists = ListaPreciosDetalle::where([
                ['id_sucursal', $id_sucursal],
                ['id_producto', $row->id_producto],
                ['estado', 1],
            ]);

            if($price_lists->count() > 0){
                $price_lists = $price_lists->get();
                $max_price = 0;
                $min_price = 9999;
                $arr_mediana   = [];

                foreach($price_lists as $k => $lista){
                    $max_price = $max_price < $lista->precio_venta? $lista->precio_venta : $max_price;
                    $min_price = $min_price > $lista->precio_venta? $lista->precio_venta : $min_price;

                    $arr_mediana[$k] = floatval($lista->precio_venta);
                }
                $mediana = $this->calculate_median($arr_mediana);

                $data[] = array(
                    "id_producto"     => $row->id_producto,
                    "codigo_producto" => $row->codigo_producto,
                    "cod_establecimiento" => $row->sucursal->cod_domicilio_fiscal,
                    "precio_1" => number_format($max_price, 2),
                    "precio_2" => number_format($min_price, 2),
                    "precio_3" => number_format($mediana, 2),
                );
            }
        }

        return response()->json(['data'=> $data, ]);
    }
    public function exportarReporteListaPrecios($id_sucursal) {
        $products = Producto::where([
            ["estado", 1],
        ]);
        if($id_sucursal){
            $products = $products->where('id_sucursal', $id_sucursal);
        }
        $products = $products->get();


        $data_arr = array();
        foreach($products as $row){
            $price_lists = ListaPreciosDetalle::where([
                ['id_sucursal', $id_sucursal],
                ['id_producto', $row->id_producto],
                ['estado', 1],
            ]);

            if($price_lists->count() > 0){
                $price_lists = $price_lists->get();
                $max_price = 0;
                $min_price = 9999;
                $arr_mediana   = [];
                foreach($price_lists as $k => $lista){
                    $max_price = $max_price < $lista->precio_venta? $lista->precio_venta : $max_price;
                    $min_price = $min_price > $lista->precio_venta? $lista->precio_venta : $min_price;

                    $arr_mediana[$k] = floatval($lista->precio_venta);
                }
                $mediana = $this->calculate_median($arr_mediana);

                $data_arr[] = array(
                    "id_producto"     => $row->id_producto,
                    "codigo_producto" => $row->codigo_producto,
                    "cod_establecimiento" => $row->sucursal->cod_domicilio_fiscal,
                    // "lista_precios" => $price_lists,
                    "precio_1" => $max_price,
                    "precio_2" => $min_price,
                    "precio_3" => $mediana,
                );
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Hoja1");

        //Rellenando Headers 
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->setCellValue('A1', 'CodEstab');
        $sheet->setCellValue('B1', 'CodProd');
        $sheet->setCellValue('C1', 'Precio 1');
        $sheet->setCellValue('D1', 'Precio 2');
        $sheet->setCellValue('E1', 'Precio 3');

        //Formato Numero
        $sheet->getStyle('C')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('D')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('E')->getNumberFormat()->setFormatCode('0.00');

        /* for($i=0; $i < $max_list_count; $i++){
            $column = chr(67 + $i);
            $sheet->getColumnDimension($column)->setWidth(20);
            $sheet->setCellValue($column.'1', 'Precio '.intval($i+1));
        }*/

        $sprst_row = 2;
        foreach($data_arr as $data){    
            $sheet->setCellValue('A'.$sprst_row, $data["cod_establecimiento"]);
            $sheet->setCellValue('B'.$sprst_row, $data["codigo_producto"]);
            $sheet->setCellValue('C'.$sprst_row, number_format($data["precio_1"], 2));
            $sheet->setCellValue('D'.$sprst_row, number_format($data["precio_2"], 2));
            $sheet->setCellValue('E'.$sprst_row, number_format($data["precio_3"], 2));
            
            /*foreach($data["lista_precios"] as $k => $lista){
                $l_column = chr(67 + $k);
                $sheet->setCellValue($l_column.$sprst_row, $lista["precio_venta"]);
            }*/

            $sprst_row+=1;
        }

        $writer = new Xlsx($spreadsheet);
        $file_name = "ReporteListaPrecios_".time().".xlsx";
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        $writer->save('php://output');
    }
    function calculate_median($arr){
        sort($arr);
        $count = count($arr);

        $middleval = floor(($count - 1) / 2);
        if ($count % 2) {
            $median = $arr[$middleval];
        } else {
            $low = $arr[$middleval];
            $high = $arr[$middleval + 1];
            $median = (($low + $high) / 2);
        }
        return $median;
    }
    //--- END ---
}
