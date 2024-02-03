<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Producto::create([
            'id_categoria' => 1,
            'id_marca' => 1,
            'id_unidad_medida' => 2,
            'id_sucursal' => 1,
            'id_laboratorio' => 1,
            'id_condicion_alm' => 1,
            'id_tipo_producto' => 1,
            'codigo_producto' => 'PR001',
            'nombreProducto' => 'Producto 1',
            'servicio' => 0,
            'stock' => 1000,
            'stock_minimo' => 100,
            'principio_activo' => null,
            'indicaciones' => null,
            'concentracion' => null,
            'registro_sanitario' => null,
            'vigencia_registro' => null,
            'ubicacion' => 'Almacen',
        ]);

        Producto::create([
            'id_categoria' => 1,
            'id_marca' => 2,
            'id_unidad_medida' => 2,
            'id_sucursal' => 1,
            'id_laboratorio' => 2,
            'id_condicion_alm' => 1,
            'id_tipo_producto' => 8,
            'codigo_producto' => 'PR002',
            'nombreProducto' => 'Producto 2',
            'servicio' => 0,
            'stock' => 1000,
            'stock_minimo' => 100,
            'principio_activo' => null,
            'indicaciones' => null,
            'concentracion' => null,
            'registro_sanitario' => null,
            'vigencia_registro' => null,
            'ubicacion' => 'Almacen',
        ]);

        Producto::create([
            'id_categoria' => 1,
            'id_marca' => 2,
            'id_unidad_medida' => 2,
            'id_sucursal' => 1,
            'id_laboratorio' => 3,
            'id_condicion_alm' => 1,
            'id_tipo_producto' => 10,
            'codigo_producto' => 'PR003',
            'nombreProducto' => 'Producto 3',
            'servicio' => 0,
            'stock' => 1000,
            'stock_minimo' => 100,
            'principio_activo' => null,
            'indicaciones' => null,
            'concentracion' => null,
            'registro_sanitario' => null,
            'vigencia_registro' => null,
            'ubicacion' => 'Almacen',
        ]);
    }
}
