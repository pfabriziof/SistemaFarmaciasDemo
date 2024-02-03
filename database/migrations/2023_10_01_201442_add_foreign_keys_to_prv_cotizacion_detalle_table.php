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
        Schema::table('prv_cotizacion_detalle', function (Blueprint $table) {
            $table->foreign(['id_cotizacion_prv'], 'CotizacionDetalle_Cotizacion_fk')->references(['id_cotizacion_prv'])->on('prv_cotizaciones');
            $table->foreign(['id_producto'], 'CotizacionDetalle_Producto_fk')->references(['id_producto'])->on('productos_servicios');
            $table->foreign(['id_unidad_medida'], 'CotizacionDetalle_UnidadMedida_fk')->references(['id_unidad_medida'])->on('unidades_medida');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prv_cotizacion_detalle', function (Blueprint $table) {
            $table->dropForeign('CotizacionDetalle_Cotizacion_fk');
            $table->dropForeign('CotizacionDetalle_Producto_fk');
            $table->dropForeign('CotizacionDetalle_UnidadMedida_fk');
        });
    }
};
