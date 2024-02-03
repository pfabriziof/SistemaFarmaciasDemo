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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->bigIncrements('id_proveedor');
            $table->unsignedTinyInteger('id_tipo_doc')->nullable()->index('Proveedor_TipoDocumento_fk');
            $table->unsignedInteger('id_sucursal')->nullable()->index('Proveedor_Sucursal_fk');
            $table->unsignedTinyInteger('tipo_proveedor')->nullable()->comment('	1=Interno, 2=Distribuidor	');
            $table->string('nombre', 250)->nullable();
            $table->string('nro_doc', 45)->nullable()->unique('nro_doc');
            $table->string('email', 250)->nullable();
            $table->string('telefono', 45)->nullable();
            $table->string('direccion', 250)->nullable();
            $table->string('contacto_nombre', 250)->nullable();
            $table->string('contacto_telefono', 45)->nullable();
            $table->integer('id_departamento')->nullable();
            $table->integer('id_provincia')->nullable();
            $table->integer('id_distrito')->nullable();
            $table->tinyInteger('estado')->nullable()->default(1)->comment('1=Habilitado, 0=Deshabilitado');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proveedores');
    }
};
