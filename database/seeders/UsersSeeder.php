<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'id_role'     => 2, //admin
            'id_sucursal' => 1,
            'name'        => 'Administrador',
            'email'       => 'admin@gmail.com',
            'password'    => Hash::make('admin2023'),
        ])->assignRole('admin');

        User::create([
            'id_role'     => 3, //contabilidad
            'id_sucursal' => 1,
            'name'        => 'Contabilidad',
            'email'       => 'contabilidad@gmail.com',
            'password'    => Hash::make('contabilidad2023'),
        ])->assignRole('accounting');

        User::create([
            'id_role'     => 4, //almacen
            'id_sucursal' => 1,
            'name'        => 'Almacén',
            'email'       => 'almacen@gmail.com',
            'password'    => Hash::make('almacen2023'),
        ])->assignRole('warehouse');

        User::create([
            'id_role'     => 5, //cajero
            'id_sucursal' => 1,
            'name'        => 'Cajero',
            'email'       => 'cajero@gmail.com',
            'password'    => Hash::make('cajero2023'),
        ])->assignRole('cashier');
    }
}
