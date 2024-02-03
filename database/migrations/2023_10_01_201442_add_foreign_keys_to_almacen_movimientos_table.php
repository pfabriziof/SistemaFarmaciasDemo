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
        Schema::table('almacen_movimientos', function (Blueprint $table) {
            $table->foreign(['id_producto'], 'Almacen_Producto_fk')->references(['id_producto'])->on('productos_servicios');
            $table->foreign(['id_sucursal'], 'Almacen_Sucursal_fk')->references(['id_sucursal'])->on('sucursales')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['id_tipo_movimiento'], 'Almacen_TipoMovimiento_fk')->references(['id_tipo_movimiento'])->on('tipos_movimiento')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['id_unidad_medida'], 'Almacen_UnidadMedida')->references(['id_unidad_medida'])->on('unidades_medida');
            $table->foreign(['id_usuario'], 'Almacen_Usuario_fk')->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('almacen_movimientos', function (Blueprint $table) {
            $table->dropForeign('Almacen_Producto_fk');
            $table->dropForeign('Almacen_Sucursal_fk');
            $table->dropForeign('Almacen_TipoMovimiento_fk');
            $table->dropForeign('Almacen_UnidadMedida');
            $table->dropForeign('Almacen_Usuario_fk');
        });
    }
};
