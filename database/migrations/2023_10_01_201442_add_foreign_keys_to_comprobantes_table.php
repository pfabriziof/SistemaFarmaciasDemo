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
        Schema::table('comprobantes', function (Blueprint $table) {
            $table->foreign(['id_cliente'], 'Comprobante_Cliente_fk')->references(['id_cliente'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['id_estado_comprobante'], 'Comprobante_Estado_fk')->references(['id_estado_comprobante'])->on('comprobante_estados');
            $table->foreign(['id_medio_pago'], 'Comprobante_MedioPago_fk')->references(['id_medio_pago'])->on('medio_pago');
            $table->foreign(['id_moneda'], 'Comprobante_Moneda_fk')->references(['id_moneda'])->on('monedas');
            $table->foreign(['id_serie'], 'Comprobante_Serie_fk')->references(['id_serie'])->on('series_inv');
            $table->foreign(['id_sucursal'], 'Comprobante_Sucursal_fk')->references(['id_sucursal'])->on('sucursales');
            $table->foreign(['id_tipo_cambio'], 'Comprobante_TipoCambio_fk')->references(['id_tipo_cambio'])->on('tipo_cambio');
            $table->foreign(['id_tipo_comprobante'], 'Comprobante_TipoComprobante_fk')->references(['id_tipo_comprobante'])->on('tipos_comprobante');
            $table->foreign(['id_usuario'], 'Comprobante_Usuario_fk')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            $table->dropForeign('Comprobante_Cliente_fk');
            $table->dropForeign('Comprobante_Estado_fk');
            $table->dropForeign('Comprobante_MedioPago_fk');
            $table->dropForeign('Comprobante_Moneda_fk');
            $table->dropForeign('Comprobante_Serie_fk');
            $table->dropForeign('Comprobante_Sucursal_fk');
            $table->dropForeign('Comprobante_TipoCambio_fk');
            $table->dropForeign('Comprobante_TipoComprobante_fk');
            $table->dropForeign('Comprobante_Usuario_fk');
        });
    }
};
