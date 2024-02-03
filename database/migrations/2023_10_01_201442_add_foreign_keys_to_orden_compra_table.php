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
        Schema::table('orden_compra', function (Blueprint $table) {
            $table->foreign(['id_medio_pago'], 'OrdenCompra_MedioPago_fk')->references(['id_medio_pago'])->on('medio_pago');
            $table->foreign(['id_moneda'], 'OrdenCompra_Moneda_fk')->references(['id_moneda'])->on('monedas');
            $table->foreign(['id_proveedor'], 'OrdenCompra_Proveedor_fk')->references(['id_proveedor'])->on('proveedores');
            $table->foreign(['id_sucursal'], 'OrdenCompra_Sucursal_fk')->references(['id_sucursal'])->on('sucursales');
            $table->foreign(['id_tipo_cambio'], 'OrdenCompra_TipoCambio_fk')->references(['id_tipo_cambio'])->on('tipo_cambio');
            $table->foreign(['id_usuario'], 'OrdenCompra_Usuario_fk')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orden_compra', function (Blueprint $table) {
            $table->dropForeign('OrdenCompra_MedioPago_fk');
            $table->dropForeign('OrdenCompra_Moneda_fk');
            $table->dropForeign('OrdenCompra_Proveedor_fk');
            $table->dropForeign('OrdenCompra_Sucursal_fk');
            $table->dropForeign('OrdenCompra_TipoCambio_fk');
            $table->dropForeign('OrdenCompra_Usuario_fk');
        });
    }
};
