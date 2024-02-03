<?php

namespace Database\Seeders;

use App\Models\ListaPrecios;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListasPreciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ListaPrecios::create([
            'codigo' => 'NIU',
            'nombre' => 'Unidad',
        ]);

        ListaPrecios::create([
            'codigo' => 'BX',
            'nombre' => 'Caja',
        ]);

        ListaPrecios::create([
            'codigo' => 'BLT',
            'nombre' => 'Blister',
        ]);
    }
}
