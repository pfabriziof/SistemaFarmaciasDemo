<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SucursalesSeeder extends Seeder
{
    public function run()
    {
        Sucursal::create([
            'id_empresa' => 1,
            'nombre_sucursal' => 'Sucursal 1',
            'cod_domicilio_fiscal' => '111',
            'direccion_fiscal' => 'dirfis2',


            'id_departamento' => 4,
            'id_provincia' => 401,
            'id_distrito' => 40101,


            'telefono' => '123456789',
            'direccion_comercial' => 'dircom1',
            'email' => 'sucursal1@gmail.com',
            'direccion_web' => 'dirweb1',
            'nro_cuenta_bancario' => '123456',
            'cci_bancario' => '1111111111',
            'api_url' => 'https://pruebas.bytesoluciones.net/',
            'api_token' => '343KdvnhncBIFKMkMcAXF5QzRUKRXAHPpW8Sp1AO5CA6SbdJKV',
        ]);

        Sucursal::create([
            'id_empresa' => 1,
            'nombre_sucursal' => 'Sucursal 2',
            'cod_domicilio_fiscal' => '222',
            'direccion_fiscal' => 'dirfis1',


            'id_departamento' => 4,
            'id_provincia' => 402,
            'id_distrito' => 40201,


            'telefono' => '987654321',
            'direccion_comercial' => 'dircom2',
            'email' => 'sucursal2@gmail.com',
            'direccion_web' => 'dirweb2',
            'nro_cuenta_bancario' => '654321',
            'cci_bancario' => '22222222222',
            'api_url' => 'https://pruebas.bytesoluciones.net/',
            'api_token' => '343KdvnhncBIFKMkMcAXF5QzRUKRXAHPpW8Sp1AO5CA6SbdJKV',
        ]);
    }
}
