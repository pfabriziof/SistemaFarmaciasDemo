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
        Schema::create('deudas_comprobantes', function (Blueprint $table) {
            $table->bigIncrements('id_deuda');
            $table->unsignedBigInteger('id_comprobante')->index('Deuda_Comprobante_fk');
            $table->unsignedBigInteger('id_cliente')->index('Deuda_Cliente_fk');
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
        Schema::dropIfExists('deudas_comprobantes');
    }
};
