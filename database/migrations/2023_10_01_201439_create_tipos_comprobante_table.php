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
        Schema::create('tipos_comprobante', function (Blueprint $table) {
            $table->tinyIncrements('id_tipo_comprobante');
            $table->string('tipo_comprobante', 45)->nullable();
            $table->string('codigo_sunat', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipos_comprobante');
    }
};
