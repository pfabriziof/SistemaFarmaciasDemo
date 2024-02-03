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
        Schema::table('productos_servicios', function (Blueprint $table) {
            $table->foreign(['id_categoria'], 'Producto_Categoria_fk')->references(['id_categoria'])->on('productos_categorias');
            $table->foreign(['id_condicion_alm'], 'Producto_CondicionAlm_fk')->references(['id_condicion_alm'])->on('condiciones_almacenamiento');
            $table->foreign(['id_laboratorio'], 'Producto_Laboratorio_fk')->references(['id_laboratorio'])->on('laboratorios');
            $table->foreign(['id_marca'], 'Producto_Marca_fk')->references(['id_marca'])->on('marcas_productos');
            $table->foreign(['id_sucursal'], 'Producto_Sucursal_fk')->references(['id_sucursal'])->on('sucursales');
            $table->foreign(['id_tipo_producto'], 'Producto_TipoProducto_fk')->references(['id_producto_tipo'])->on('producto_tipos');
            $table->foreign(['id_unidad_medida'], 'Producto_UnidadMedida_fk')->references(['id_unidad_medida'])->on('unidades_medida');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productos_servicios', function (Blueprint $table) {
            $table->dropForeign('Producto_Categoria_fk');
            $table->dropForeign('Producto_CondicionAlm_fk');
            $table->dropForeign('Producto_Laboratorio_fk');
            $table->dropForeign('Producto_Marca_fk');
            $table->dropForeign('Producto_Sucursal_fk');
            $table->dropForeign('Producto_TipoProducto_fk');
            $table->dropForeign('Producto_UnidadMedida_fk');
        });
    }
};
