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
        Schema::create('caja', function (Blueprint $table) {
            $table->bigIncrements('id_caja');
            $table->unsignedInteger('id_sucursal')->nullable()->index('Caja_Sucursal_fk');
            $table->unsignedBigInteger('id_usuario')->nullable()->index('Caja_Usuario_fk');
            $table->timestamp('fecha_apertura')->nullable();
            $table->timestamp('fecha_cierre')->nullable();
            $table->decimal('monto_apertura', 11)->nullable();
            $table->decimal('monto_cierre', 11)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caja');
    }
};
