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
        Schema::create('comprobante_detalle', function (Blueprint $table) {
            $table->bigIncrements('id_comp_detalle');
            $table->unsignedBigInteger('id_comprobante')->nullable()->index('ComprobanteDetalle_Comprobante_fk');
            $table->unsignedBigInteger('id_producto')->nullable()->index('ComprobanteDetalle_Producto_fk');
            $table->string('nombre_producto', 250)->nullable();
            $table->unsignedInteger('id_unidad_medida')->nullable()->index('ComprobanteDetalle_UnidadMedida_fk');
            $table->string('und_simbolo', 45)->nullable();
            $table->unsignedBigInteger('id_lista_detalle')->nullable()->index('ComprobanteDetalle_ListaDetalle_fk');
            $table->unsignedBigInteger('id_lote')->nullable()->index('ComprobanteDetalle_Lote_fk');
            $table->string('lote_producto', 250)->nullable();
            $table->decimal('precio_unitario', 11)->nullable();
            $table->decimal('cantidad', 11)->nullable();
            $table->decimal('cantidad_visual', 11)->nullable();
            $table->decimal('precio_total', 11)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comprobante_detalle');
    }
};
