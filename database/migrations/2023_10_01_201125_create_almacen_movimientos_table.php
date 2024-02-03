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
        Schema::create('almacen_movimientos', function (Blueprint $table) {
            $table->bigIncrements('id_almacen_movimientos');
            $table->unsignedInteger('id_sucursal')->index('Almacen_Sucursal_fk');
            $table->unsignedBigInteger('id_usuario')->index('Almacen_Usuario_fk');
            $table->unsignedBigInteger('id_producto')->index('Almacen_Producto_fk');
            $table->string('NombreProducto', 250)->nullable();
            $table->unsignedTinyInteger('id_tipo_movimiento')->index('Almacen_TipoMovimiento_fk');
            $table->unsignedInteger('id_unidad_medida')->index('Almacen_UnidadMedida');
            $table->string('und_simbolo', 250)->nullable();
            $table->decimal('cantidad', 11)->nullable();
            $table->decimal('precioUnitario', 11)->nullable();
            $table->decimal('precioTotal', 11)->nullable();
            $table->decimal('stock_actual', 11)->nullable();
            $table->date('fecha_movimiento')->nullable();
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
        Schema::dropIfExists('almacen_movimientos');
    }
};
