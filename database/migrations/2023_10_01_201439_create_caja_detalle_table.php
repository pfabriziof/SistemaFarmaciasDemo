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
        Schema::create('caja_detalle', function (Blueprint $table) {
            $table->bigIncrements('id_caja_det');
            $table->unsignedBigInteger('id_caja')->nullable()->index('CajaDetalle_Caja_fk');
            $table->unsignedTinyInteger('id_medio_pago')->nullable();
            $table->decimal('monto', 11)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caja_detalle');
    }
};
