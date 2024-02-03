<?php

namespace Database\Seeders;

use App\Models\TipoComprobante;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposComprobantesSeeder extends Seeder
{
    public function run()
    {
        TipoComprobante::create([
            'id_tipo_comprobante' => 1,
            'tipo_comprobante' => 'Factura',
            'codigo_sunat' => '01',
        ]);

        TipoComprobante::create([
            'id_tipo_comprobante' => 2,
            'tipo_comprobante' => 'Boleta',
            'codigo_sunat' => '03',
        ]);
    }
}