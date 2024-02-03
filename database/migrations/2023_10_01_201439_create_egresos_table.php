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
        Schema::create('egresos', function (Blueprint $table) {
            $table->bigIncrements('id_egreso');
            $table->unsignedInteger('id_sucursal')->nullable()->index('Egreso_Sucursal_fk');
            $table->unsignedBigInteger('id_usuario')->nullable()->index('Egreso_Usuario_fk');
            $table->unsignedBigInteger('id_compra')->nullable()->index('Egreso_Compra_fk');
            $table->unsignedTinyInteger('id_tipo_egreso')->nullable()->index('Egreso_TipoEgreso_fk');
            $table->unsignedTinyInteger('id_motivo_egreso')->nullable()->index('Egreso_MotivoEgreso_fk');
            $table->unsignedTinyInteger('metodo_gasto')->nullable()->comment('1=Caja Chica, 2=Cuenta Bancaria');
            $table->dateTime('fecha_egreso')->nullable();
            $table->decimal('monto', 11)->nullable();
            $table->string('detalle', 150)->nullable();
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
        Schema::dropIfExists('egresos');
    }
};
