<?php

namespace Database\Seeders;

use App\Models\CompraEstado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompraEstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CompraEstado::create([
            'estado' => 'Aprobada',
        ]);
        CompraEstado::create([
            'estado' => 'Desestimada',
        ]);
        CompraEstado::create([
            'estado' => 'En espera',
        ]);
    }
}
