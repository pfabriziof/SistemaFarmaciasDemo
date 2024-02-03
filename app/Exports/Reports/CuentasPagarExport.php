<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CuentasPagarExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $params_export;

    function __construct($params) {
        $this->params_export = $params;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Nro. Comprobante',
            'Proveedor',
            'Monto Deuda Pendiente',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function collection()
    {
        $searchTerm   = $this->params_export["searchTerm"];
        $fechaInicio = $this->params_export["fechaInicio"];
        $fechaFin    = $this->params_export["fechaFin"];

        $datos = DB::table('deudas_compras')
            ->join('compras', 'deudas_compras.id_compra', '=', 'compras.id_compra')
            ->join('proveedores', 'deudas_compras.id_proveedor', '=', 'proveedores.id_proveedor')
            ->select(DB::raw('
                compras.fecha_emision,
                CONCAT(compras.serie_factura, "-", compras.nro_factura),
                proveedores.nombre,
                deudas_compras.total_monto_pendiente'));

        if(isset($searchTerm)){
            $datos = $datos->where(function ($query) use ($searchTerm) {
                $query->where('proveedores.nombre', 'like', "%{$searchTerm}%")
                      ->orWhere('proveedores.nro_doc', 'like', "%{$searchTerm}%");
            });
        }
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $datos = $datos->whereBetween('compras.fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $datos = $datos->where('compras.fecha_emision', '>=', $fechaInicio);
            }
        }

        return $datos->groupBy('deudas_compras.id_deuda')->get();
    }
}