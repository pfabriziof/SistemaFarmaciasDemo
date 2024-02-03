<?php

namespace Database\Seeders;

use App\Models\EgresoMotivo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EgresosMotivosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EgresoMotivo::create([
            'motivo' => 'Representación de la empresa'
        ]);
        EgresoMotivo::create([
            'motivo' => 'Trabajo de campo'
        ]);
        EgresoMotivo::create([
            'motivo' => 'Varios'
        ]);
    }
}
