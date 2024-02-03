<?php

namespace Database\Seeders;

use App\Models\CompressedTable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompressedTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CompressedTable::create([
            'title' => 'Productos',
            'table_name' => 'productos_servicios',
            'query' => "TABLE productos_servicios(id_producto bigint,codigo_producto varchar,id_marca bigint,id_categoria int,id_unidad_medida int,id_sucursal int,nombreProducto varchar,stock decimal,servicio tinyint COMMENT '0=no, 1=si',registro_sanitario varchar,vigencia_registro date,estado tinyint COMMENT '0=inactivo, 1=activo')
            TABLE marcas_productos (id_marca bigint,marca varchar, estado tinyint COMMENT '0=inactivo, 1=activo')",
        ]);
    }
}
