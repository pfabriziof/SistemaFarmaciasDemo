<?php

namespace Database\Seeders;

use App\Models\TipoCambio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposCambioSeeder extends Seeder
{
    public function run()
    {
        TipoCambio::create([
            'tipo_cambio' => 'Dólares',
            'cambio' => null,
        ]);
    }
}
