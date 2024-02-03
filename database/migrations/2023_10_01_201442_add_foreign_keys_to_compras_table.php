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
        Schema::table('compras', function (Blueprint $table) {
            $table->foreign(['id_estado'], 'Compra_CompraEstado_fk')->references(['id'])->on('compra_estado');
            $table->foreign(['id_medio_pago'], 'Compra_MedioPago_fk')->references(['id_medio_pago'])->on('medio_pago');
            $table->foreign(['id_moneda'], 'Compra_Moneda_fk')->references(['id_moneda'])->on('monedas');
            $table->foreign(['id_proveedor'], 'Compra_Proveedor_fk')->references(['id_proveedor'])->on('proveedores');
            $table->foreign(['id_sucursal'], 'Compra_Sucursal_fk')->references(['id_sucursal'])->on('sucursales');
            $table->foreign(['id_tipo_cambio'], 'Compra_TipoCambio_fk')->references(['id_tipo_cambio'])->on('tipo_cambio');
            $table->foreign(['id_tipo_comprobante'], 'Compra_TipoComprobante_fk')->references(['id_tipo_comprobante'])->on('tipos_comprobante');
            $table->foreign(['id_usuario'], 'Compra_Usuario_fk')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropForeign('Compra_CompraEstado_fk');
            $table->dropForeign('Compra_MedioPago_fk');
            $table->dropForeign('Compra_Moneda_fk');
            $table->dropForeign('Compra_Proveedor_fk');
            $table->dropForeign('Compra_Sucursal_fk');
            $table->dropForeign('Compra_TipoCambio_fk');
            $table->dropForeign('Compra_TipoComprobante_fk');
            $table->dropForeign('Compra_Usuario_fk');
        });
    }
};
