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
        Schema::table('compras_detalle', function (Blueprint $table) {
            $table->foreign(['id_compra'], 'CompraDetalle_Compra_fk')->references(['id_compra'])->on('compras');
            $table->foreign(['id_lista_detalle'], 'CompraDetalle_ListaDetalle_fk')->references(['id_lista_detalle'])->on('lista_precios_detalle');
            $table->foreign(['lote_id'], 'CompraDetalle_Lote_fk')->references(['id_lote'])->on('lote_productos');
            $table->foreign(['id_producto'], 'CompraDetalle_Producto_fk')->references(['id_producto'])->on('productos_servicios');
            $table->foreign(['id_unidad_medida'], 'CompraDetalle_UnidadMedida_fk')->references(['id_unidad_medida'])->on('unidades_medida');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compras_detalle', function (Blueprint $table) {
            $table->dropForeign('CompraDetalle_Compra_fk');
            $table->dropForeign('CompraDetalle_ListaDetalle_fk');
            $table->dropForeign('CompraDetalle_Lote_fk');
            $table->dropForeign('CompraDetalle_Producto_fk');
            $table->dropForeign('CompraDetalle_UnidadMedida_fk');
        });
    }
};
