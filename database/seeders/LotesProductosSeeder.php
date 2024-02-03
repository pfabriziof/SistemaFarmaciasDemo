<?php

namespace Database\Seeders;

use App\Models\LoteProducto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LotesProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LoteProducto::create([
            'lote' => 'L1001',
            'cantidad' => 1000,
            'fecha_expiracion' => '2024-12-31',
            'id_producto' => 1,
            'id_sucursal' => 1
        ]);

        LoteProducto::create([
            'lote' => 'L2001',
            'cantidad' => 1000,
            'fecha_expiracion' => '2024-12-31',
            'id_producto' => 2,
            'id_sucursal' => 1
        ]);

        LoteProducto::create([
            'lote' => 'L3001',
            'cantidad' => 1000,
            'fecha_expiracion' => '2024-12-31',
            'id_producto' => 3,
            'id_sucursal' => 1
        ]);
    }
}
