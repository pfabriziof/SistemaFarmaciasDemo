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
        Schema::create('deudas_compras', function (Blueprint $table) {
            $table->bigIncrements('id_deuda');
            $table->unsignedBigInteger('id_compra')->index('DeudaCompra_Compra_fk');
            $table->unsignedBigInteger('id_proveedor')->index('DeudaCompra_Proveedor_fk');
            $table->decimal('total_adelanto', 11)->nullable();
            $table->decimal('total_deuda', 11)->nullable();
            $table->decimal('total_monto_pagado', 11)->nullable();
            $table->decimal('total_monto_pendiente', 11)->nullable();
            $table->date('fecha')->nullable();
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
        Schema::dropIfExists('deudas_compras');
    }
};
