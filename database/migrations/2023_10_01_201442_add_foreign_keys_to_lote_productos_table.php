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
        Schema::table('lote_productos', function (Blueprint $table) {
            $table->foreign(['id_producto'], 'Lote_Producto_fk')->references(['id_producto'])->on('productos_servicios');
            $table->foreign(['id_sucursal'], 'Lote_Sucursal_fk')->references(['id_sucursal'])->on('sucursales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lote_productos', function (Blueprint $table) {
            $table->dropForeign('Lote_Producto_fk');
            $table->dropForeign('Lote_Sucursal_fk');
        });
    }
};
