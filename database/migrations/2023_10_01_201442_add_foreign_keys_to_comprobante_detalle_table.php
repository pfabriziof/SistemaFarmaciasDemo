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
        Schema::table('comprobante_detalle', function (Blueprint $table) {
            $table->foreign(['id_comprobante'], 'ComprobanteDetalle_Comprobante_fk')->references(['id_comprobante'])->on('comprobantes');
            $table->foreign(['id_lista_detalle'], 'ComprobanteDetalle_ListaDetalle_fk')->references(['id_lista_detalle'])->on('lista_precios_detalle');
            $table->foreign(['id_lote'], 'ComprobanteDetalle_Lote_fk')->references(['id_lote'])->on('lote_productos');
            $table->foreign(['id_producto'], 'ComprobanteDetalle_Producto_fk')->references(['id_producto'])->on('productos_servicios');
            $table->foreign(['id_unidad_medida'], 'ComprobanteDetalle_UnidadMedida_fk')->references(['id_unidad_medida'])->on('unidades_medida');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comprobante_detalle', function (Blueprint $table) {
            $table->dropForeign('ComprobanteDetalle_Comprobante_fk');
            $table->dropForeign('ComprobanteDetalle_ListaDetalle_fk');
            $table->dropForeign('ComprobanteDetalle_Lote_fk');
            $table->dropForeign('ComprobanteDetalle_Producto_fk');
            $table->dropForeign('ComprobanteDetalle_UnidadMedida_fk');
        });
    }
};
