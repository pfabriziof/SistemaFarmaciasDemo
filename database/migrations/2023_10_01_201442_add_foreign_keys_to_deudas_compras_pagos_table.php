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
        Schema::table('deudas_compras_pagos', function (Blueprint $table) {
            $table->foreign(['id_deuda'], 'DeudaPago_Pago_fk')->references(['id_deuda'])->on('deudas_compras');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deudas_compras_pagos', function (Blueprint $table) {
            $table->dropForeign('DeudaPago_Pago_fk');
        });
    }
};
