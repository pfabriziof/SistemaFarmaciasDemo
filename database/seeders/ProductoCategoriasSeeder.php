<?php

namespace Database\Seeders;

use App\Models\ProductoCategoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductoCategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductoCategoria::create([
            'codigo' => 'categoria_1',
            'categoria' => 'Categoria 1',
        ]);

        ProductoCategoria::create([
            'codigo' => 'categoria_2',
            'categoria' => 'Categoria 2',
        ]);

        ProductoCategoria::create([
            'codigo' => 'categoria_3',
            'categoria' => 'Categoria 3',
        ]);
    }
}
