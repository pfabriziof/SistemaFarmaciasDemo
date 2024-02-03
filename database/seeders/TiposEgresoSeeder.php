<?php

namespace Database\Seeders;

use App\Models\TipoEgreso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposEgresoSeeder extends Seeder
{
    public function run()
    {
        TipoEgreso::create([
            'id_tipo_egreso' => 1,
            'tipo_egreso' => 'Planilla',
        ]);
        TipoEgreso::create([
            'id_tipo_egreso' => 2,
            'tipo_egreso' => 'Recibo por Honorarios',
        ]);
        TipoEgreso::create([
            'id_tipo_egreso' => 3,
            'tipo_egreso' => 'Servicios',
        ]);
        TipoEgreso::create([
            'id_tipo_egreso' => 4,
            'tipo_egreso' => 'Otros',
        ]);
    }
}
