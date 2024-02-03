<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnidadesMedidaSeeder extends Seeder
{
    public function run()
    {
        UnidadMedida::create([
            'unidad_medida' => 'Servicio',
            'codigo_sunat' => 'ZZ',
            'simbolo' => 'SERV'
        ]);
        UnidadMedida::create([
            'unidad_medida' => 'Unidad',
            'codigo_sunat' => 'NIU',
            'simbolo' => 'UND'
        ]);
        UnidadMedida::create([
            'unidad_medida' => 'Caja',
            'codigo_sunat' => 'BX',
            'simbolo' => 'CAJA'
        ]);
        UnidadMedida::create([
            'unidad_medida' => 'Galones',
            'codigo_sunat' => 'GLL',
            'simbolo' => 'GAL'
        ]);
        UnidadMedida::create([
            'unidad_medida' => 'Gramos',
            'codigo_sunat' => 'GRM',
            'simbolo' => 'GR'
        ]);
        UnidadMedida::create([
            'unidad_medida' => 'Kilos',
            'codigo_sunat' => 'KG',
            'simbolo' => 'KG'
        ]);
    }
}
