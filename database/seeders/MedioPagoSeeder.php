<?php

namespace Database\Seeders;

use App\Models\MedioPago;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedioPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MedioPago::create([
            'medio_pago' => 'Efectivo',
        ]);

        MedioPago::create([
            'medio_pago' => 'Tarjeta',
        ]);

        MedioPago::create([
            'medio_pago' => 'Depósito',
        ]);
    }
}
