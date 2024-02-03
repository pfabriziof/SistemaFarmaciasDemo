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
        Schema::table('lista_precios_detalle', function (Blueprint $table) {
            $table->foreign(['id_lista_precio'], 'ListaPDetalle_ListaPrecio_fk')->references(['id_lista_precio'])->on('lista_precios');
            $table->foreign(['id_producto'], 'ListaPDetalle_Producto_fk')->references(['id_producto'])->on('productos_servicios');
            $table->foreign(['id_sucursal'], 'ListaPDetalle_Sucursal_fk')->references(['id_sucursal'])->on('sucursales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lista_precios_detalle', function (Blueprint $table) {
            $table->dropForeign('ListaPDetalle_ListaPrecio_fk');
            $table->dropForeign('ListaPDetalle_Producto_fk');
            $table->dropForeign('ListaPDetalle_Sucursal_fk');
        });
    }
};
