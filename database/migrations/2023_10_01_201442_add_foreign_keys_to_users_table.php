<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign(['id_role'], 'Usuario_Rol_fk')->references(['id'])->on('roles');
            $table->foreign(['id_sucursal'], 'Usuario_Sucursal_fk')->references(['id_sucursal'])->on('sucursales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('Usuario_Rol_fk');
            $table->dropForeign('Usuario_Sucursal_fk');
        });
    }
};
