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
        Schema::table('deudas_comprobantes', function (Blueprint $table) {
            $table->foreign(['id_cliente'], 'Deuda_Cliente_fk')->references(['id_cliente'])->on('clientes');
            $table->foreign(['id_comprobante'], 'Deuda_Comprobante_fk')->references(['id_comprobante'])->on('comprobantes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deudas_comprobantes', function (Blueprint $table) {
            $table->dropForeign('Deuda_Cliente_fk');
            $table->dropForeign('Deuda_Comprobante_fk');
        });
    }
};
