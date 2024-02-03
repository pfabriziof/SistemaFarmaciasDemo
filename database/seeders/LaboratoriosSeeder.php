<?php

namespace Database\Seeders;

use App\Models\Laboratorio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LaboratoriosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Laboratorio::create([
            'nombre' => 'Laboratorio 1',
        ]);

        Laboratorio::create([
            'nombre' => 'Laboratorio 2',
        ]);

        Laboratorio::create([
            'nombre' => 'Laboratorio 3',
        ]);
    }
}
