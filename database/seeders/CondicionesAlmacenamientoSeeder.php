<?php

namespace Database\Seeders;

use App\Models\CondicionAlmacenamiento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CondicionesAlmacenamientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CondicionAlmacenamiento::create([
            'descripcion' => 'Ambiente',
        ]);

        CondicionAlmacenamiento::create([
            'descripcion' => 'Congelado',
        ]);

        CondicionAlmacenamiento::create([
            'descripcion' => 'Temperatura Fría',
        ]);
    }
}
