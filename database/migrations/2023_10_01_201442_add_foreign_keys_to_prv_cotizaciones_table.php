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
        Schema::table('prv_cotizaciones', function (Blueprint $table) {
            $table->foreign(['id_estado'], 'Cotizacion_Estado_fk')->references(['id'])->on('compra_estado');
            $table->foreign(['id_proveedor'], 'Cotizacion_Proveedor_fk')->references(['id_proveedor'])->on('proveedores');
            $table->foreign(['id_sucursal'], 'Cotizacion_Sucursal_fk')->references(['id_sucursal'])->on('sucursales');
            $table->foreign(['id_usuario'], 'Cotizacion_Usuario_fk')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prv_cotizaciones', function (Blueprint $table) {
            $table->dropForeign('Cotizacion_Estado_fk');
            $table->dropForeign('Cotizacion_Proveedor_fk');
            $table->dropForeign('Cotizacion_Sucursal_fk');
            $table->dropForeign('Cotizacion_Usuario_fk');
        });
    }
};
