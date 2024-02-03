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
        Schema::create('prv_cotizaciones', function (Blueprint $table) {
            $table->bigIncrements('id_cotizacion_prv');
            $table->unsignedBigInteger('id_usuario')->nullable()->index('Cotizacion_Usuario_fk');
            $table->unsignedInteger('id_sucursal')->nullable()->index('Cotizacion_Sucursal_fk');
            $table->unsignedBigInteger('id_proveedor')->index('Cotizacion_Proveedor_fk');
            $table->bigInteger('numeracion')->nullable();
            $table->string('email', 250)->nullable();
            $table->date('fecha_emision')->nullable();
            $table->unsignedTinyInteger('id_estado')->default(3)->index('Cotizacion_Estado_fk')->comment('1=Aprobada, 2=Desestimada, 3=Espera');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prv_cotizaciones');
    }
};
