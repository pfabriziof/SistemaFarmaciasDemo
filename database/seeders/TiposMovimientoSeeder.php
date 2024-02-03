<?php

namespace Database\Seeders;

use App\Models\TipoMovimiento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposMovimientoSeeder extends Seeder
{
    public function run()
    {
        TipoMovimiento::create([
            'id_tipo_movimiento' => 1,
            'tipo_movimiento' => 'Entrada',
        ]);
        TipoMovimiento::create([
            'id_tipo_movimiento' => 2,
            'tipo_movimiento' => 'Salida',
        ]);
        TipoMovimiento::create([
            'id_tipo_movimiento' => 3,
            'tipo_movimiento' => 'Devolución',
        ]);
        TipoMovimiento::create([
            'id_tipo_movimiento' => 5,
            'tipo_movimiento' => 'Stock Inicial',
        ]);
    }
}
