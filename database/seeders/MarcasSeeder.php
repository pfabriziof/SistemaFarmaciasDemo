<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarcasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Marca::create([
            'marca' => 'S/M',
        ]);

        Marca::create([
            'marca' => 'Marca 1',
        ]);

        Marca::create([
            'marca' => 'Marca 2',
        ]);

        Marca::create([
            'marca' => 'Marca 3',
        ]);
    }
}
