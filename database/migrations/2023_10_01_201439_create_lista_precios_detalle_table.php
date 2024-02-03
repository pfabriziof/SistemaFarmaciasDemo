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
        Schema::create('lista_precios_detalle', function (Blueprint $table) {
            $table->bigIncrements('id_lista_detalle');
            $table->unsignedInteger('id_lista_precio')->index('ListaPDetalle_ListaPrecio_fk');
            $table->unsignedBigInteger('id_producto')->index('ListaPDetalle_Producto_fk');
            $table->unsignedInteger('id_sucursal')->nullable()->index('ListaPDetalle_Sucursal_fk');
            $table->decimal('precio_venta', 11)->nullable();
            $table->decimal('precio_compra', 11)->nullable();
            $table->integer('unidades')->default(1);
            $table->tinyInteger('estado')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lista_precios_detalle');
    }
};
