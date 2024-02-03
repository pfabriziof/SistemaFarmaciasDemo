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
        Schema::create('sucursales', function (Blueprint $table) {
            $table->increments('id_sucursal');
            $table->unsignedBigInteger('id_empresa')->nullable()->index('Sucursal_Empresa_fk');
            $table->string('nombre_sucursal', 250)->nullable();
            $table->string('cod_domicilio_fiscal', 250)->nullable();
            $table->string('direccion_fiscal', 250)->nullable();
            $table->integer('id_departamento')->nullable();
            $table->integer('id_provincia')->nullable();
            $table->integer('id_distrito')->nullable();
            $table->string('telefono', 45)->nullable();
            $table->string('direccion_comercial', 250)->nullable();
            $table->string('email', 250)->nullable();
            $table->string('direccion_web', 250)->nullable();
            $table->string('nro_cuenta_bancario', 250)->nullable();
            $table->string('cci_bancario', 250)->nullable();
            $table->string('api_url', 250)->nullable();
            $table->string('api_token', 250)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sucursales');
    }
};
