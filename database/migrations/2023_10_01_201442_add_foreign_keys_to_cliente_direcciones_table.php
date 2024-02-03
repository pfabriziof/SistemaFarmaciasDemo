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
        Schema::table('cliente_direcciones', function (Blueprint $table) {
            $table->foreign(['id_cliente'], 'ClienteDireccion_Cliente')->references(['id_cliente'])->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cliente_direcciones', function (Blueprint $table) {
            $table->dropForeign('ClienteDireccion_Cliente');
        });
    }
};
