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
        Schema::create('prv_cotizacion_detalle', function (Blueprint $table) {
            $table->bigIncrements('id_cotz_detalle_prv');
            $table->unsignedBigInteger('id_cotizacion_prv')->index('CotizacionDetalle_Cotizacion_fk');
            $table->unsignedBigInteger('id_producto')->index('CotizacionDetalle_Producto_fk');
            $table->string('nombre_producto', 250)->nullable();
            $table->unsignedInteger('id_unidad_medida')->nullable()->index('CotizacionDetalle_UnidadMedida_fk');
            $table->string('und_simbolo', 45)->nullable();
            $table->decimal('cantidad', 11)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prv_cotizacion_detalle');
    }
};
