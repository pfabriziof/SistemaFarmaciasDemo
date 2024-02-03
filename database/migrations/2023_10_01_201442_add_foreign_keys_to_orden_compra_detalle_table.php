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
        Schema::table('orden_compra_detalle', function (Blueprint $table) {
            $table->foreign(['id_lista_detalle'], 'OrdComprDetalle_ListaDetalle_fk')->references(['id_lista_detalle'])->on('lista_precios_detalle');
            $table->foreign(['id_orden_compra'], 'OrdComprDetalle_OrdenCompra_fk')->references(['id_orden_compra'])->on('orden_compra');
            $table->foreign(['id_producto'], 'OrdComprDetalle_Producto_fk')->references(['id_producto'])->on('productos_servicios');
            $table->foreign(['id_unidad_medida'], 'OrdComprDetalle_UnidadMedida_fk')->references(['id_unidad_medida'])->on('unidades_medida');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orden_compra_detalle', function (Blueprint $table) {
            $table->dropForeign('OrdComprDetalle_ListaDetalle_fk');
            $table->dropForeign('OrdComprDetalle_OrdenCompra_fk');
            $table->dropForeign('OrdComprDetalle_Producto_fk');
            $table->dropForeign('OrdComprDetalle_UnidadMedida_fk');
        });
    }
};
