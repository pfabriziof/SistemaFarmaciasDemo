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
        Schema::create('productos_servicios', function (Blueprint $table) {
            $table->bigIncrements('id_producto');
            $table->string('codigo_producto', 250)->nullable();
            $table->unsignedBigInteger('id_marca')->nullable()->index('Producto_Marca_fk');
            $table->unsignedInteger('id_categoria')->index('Producto_Categoria_fk');
            $table->unsignedInteger('id_unidad_medida')->nullable()->index('Producto_UnidadMedida_fk');
            $table->unsignedInteger('id_sucursal')->nullable()->index('Producto_Sucursal_fk');
            $table->unsignedBigInteger('id_laboratorio')->nullable()->index('Producto_Laboratorio_fk');
            $table->unsignedInteger('id_condicion_alm')->nullable()->index('Producto_CondicionAlm_fk');
            $table->unsignedTinyInteger('id_tipo_producto')->nullable()->index('Producto_TipoProducto_fk');
            $table->string('nombreProducto', 250)->nullable();
            $table->decimal('stock', 11)->nullable()->default(1);
            $table->decimal('stock_minimo', 11)->nullable()->default(1);
            $table->tinyInteger('servicio')->nullable()->default(0)->comment('0=no, 1=si');
            $table->string('principio_activo', 250)->nullable();
            $table->string('indicaciones', 250)->nullable();
            $table->string('concentracion', 250)->nullable();
            $table->string('registro_sanitario', 250)->nullable();
            $table->date('vigencia_registro')->nullable();
            $table->string('ubicacion', 250)->nullable();
            $table->tinyInteger('estado')->default(1);
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
        Schema::dropIfExists('productos_servicios');
    }
};
