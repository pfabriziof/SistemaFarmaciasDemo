<?php

namespace Database\Seeders;

use App\Models\SerieInv;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeriesInvoiceSeeder extends Seeder
{
    public function run()
    {
        SerieInv::create([
            'id_sucursal'=> 1,
            'id_tipo_comprobante'=> 1,
            'serie'=> 'F001',
        ]);

        SerieInv::create([
            'id_sucursal'=> 1,
            'id_tipo_comprobante'=> 2,
            'serie'=> 'B001',
        ]);

        SerieInv::create([
            'id_sucursal'=> 2,
            'id_tipo_comprobante'=> 1,
            'serie'=> 'F002',
        ]);

        SerieInv::create([
            'id_sucursal'=> 2,
            'id_tipo_comprobante'=> 2,
            'serie'=> 'B002',
        ]);
    }
}
