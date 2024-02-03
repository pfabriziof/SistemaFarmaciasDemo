<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CuentasCobrarExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    protected $params_export;

    function __construct($params) {
        $this->params_export = $params;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Nro. Comprobante',
            'Cliente',
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

        $datos = DB::table('deudas_comprobantes')
            ->join('comprobantes', 'deudas_comprobantes.id_comprobante', '=', 'comprobantes.id_comprobante')
            ->join('clientes', 'deudas_comprobantes.id_cliente', '=', 'clientes.id_cliente')

            ->join('series_inv', 'comprobantes.id_serie', '=', 'series_inv.id_serie')
            ->join('tipos_comprobante', 'comprobantes.id_tipo_comprobante', '=', 'tipos_comprobante.id_tipo_comprobante')

            ->select(DB::raw('
                comprobantes.fecha_emision,
                CONCAT(series_inv.serie, "-", comprobantes.correlativo),
                clientes.nombre,
                deudas_comprobantes.total_monto_pendiente'));

        if(isset($searchTerm)){
            $datos = $datos->where(function ($query) use ($searchTerm) {
                $query->where('clientes.nombre', 'like', "%{$searchTerm}%")
                      ->orWhere('clientes.nro_doc', 'like', "%{$searchTerm}%");
            });
        }
        if(isset($fechaInicio)){
            if(isset($fechaFin)){
                $datos = $datos->whereBetween('comprobantes.fecha_emision', [$fechaInicio, $fechaFin]);
            }else{
                $datos = $datos->where('comprobantes.fecha_emision', '>=', $fechaInicio);
            }
        }

        return $datos->groupBy('deudas_comprobantes.id_deuda')->get();
    }
}