<?php

namespace Database\Seeders;

use App\Models\Moneda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonedasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Moneda::create([
            'moneda' => 'PEN',
            'nombre' => 'Soles',
            'simbolo' => 'S/.',
        ]);
        Moneda::create([
            'moneda' => 'USD',
            'nombre' => 'Dólares',
            'simbolo' => '$',
        ]);
    }
}
