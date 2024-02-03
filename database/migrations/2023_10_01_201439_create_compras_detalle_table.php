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
        Schema::create('compras_detalle', function (Blueprint $table) {
            $table->bigIncrements('id_compra_detalle');
            $table->unsignedBigInteger('id_compra')->nullable()->index('CompraDetalle_Compra_fk');
            $table->unsignedBigInteger('id_producto')->nullable()->index('CompraDetalle_Producto_fk');
            $table->string('nombre_producto', 250)->nullable();
            $table->unsignedInteger('id_unidad_medida')->nullable()->index('CompraDetalle_UnidadMedida_fk');
            $table->string('und_simbolo', 250)->nullable();
            $table->unsignedBigInteger('id_lista_detalle')->nullable()->index('CompraDetalle_ListaDetalle_fk');
            $table->unsignedBigInteger('lote_id')->nullable()->index('CompraDetalle_Lote_fk');
            $table->string('lote_name', 250)->nullable();
            $table->date('lote_fecha_exp')->nullable();
            $table->decimal('cantidad', 11)->nullable();
            $table->decimal('cantidad_visual', 11)->nullable();
            $table->decimal('precio_unitario', 11)->nullable();
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
        Schema::dropIfExists('compras_detalle');
    }
};
