<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedoresSeeder extends Seeder
{
    public function run()
    {
        Proveedor::create([
            'id_tipo_doc' => 1,
            'id_sucursal' => 1,
            'tipo_proveedor' => 1,
            'nombre' => 'PROVEEDOR INTERNO',
            'nro_doc' => 12345678912,
            'direccion' => 'Calle los girasoles 123',
            'email' => 'proveedorinterno@gmail.com',
            'telefono' => 123456789,

            'contacto_nombre' => null,
            'contacto_telefono' => null,

            'id_departamento' => 4,
            'id_provincia' => 401,
            'id_distrito' => 40101,
        ]);

        Proveedor::create([
            'id_tipo_doc' => 1,
            'id_sucursal' => 1,
            'tipo_proveedor' => 2,
            'nombre' => 'PROVEEDOR DISTRIBUIDOR',
            'nro_doc' => 98765432198,
            'direccion' => 'Calle los girasoles 987',
            'email' => 'proveedordistribuidor@gmail.com',
            'telefono' => 987654321,

            'contacto_nombre' => null,
            'contacto_telefono' => null,

            'id_departamento' => 4,
            'id_provincia' => 401,
            'id_distrito' => 40101,
        ]);
    }
}
