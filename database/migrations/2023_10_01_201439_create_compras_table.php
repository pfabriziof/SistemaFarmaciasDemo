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
        Schema::create('compras', function (Blueprint $table) {
            $table->bigIncrements('id_compra');
            $table->unsignedBigInteger('id_usuario')->nullable()->index('Compra_Usuario_fk');
            $table->unsignedInteger('id_sucursal')->nullable()->index('Compra_Sucursal_fk');
            $table->unsignedBigInteger('id_proveedor')->nullable()->index('Compra_Proveedor_fk');
            $table->unsignedTinyInteger('id_moneda')->nullable()->index('Compra_Moneda_fk');
            $table->unsignedTinyInteger('id_medio_pago')->nullable()->index('Compra_MedioPago_fk');
            $table->unsignedTinyInteger('id_tipo_cambio')->nullable()->index('Compra_TipoCambio_fk');
            $table->unsignedTinyInteger('id_tipo_comprobante')->nullable()->index('Compra_TipoComprobante_fk');
            $table->bigInteger('correlativo')->nullable();
            $table->string('nombreProveedor', 250)->nullable();
            $table->string('nroDocProveedor', 45)->nullable();
            $table->string('email', 250)->nullable();
            $table->string('nro_guia_remision', 45)->nullable();
            $table->string('serie_factura', 45)->nullable();
            $table->string('nro_factura', 45)->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->tinyInteger('origen_dinero')->nullable()->comment('1=Caja Chica, 2=Cuenta Bancaria, 3=Otra Fuente');
            $table->decimal('op_inafectas', 11)->nullable();
            $table->decimal('op_exoneradas', 11)->nullable();
            $table->decimal('op_gravadas', 11)->nullable();
            $table->decimal('icbper', 11)->nullable();
            $table->decimal('porcentaje_igv', 11)->nullable();
            $table->decimal('igv', 11)->nullable();
            $table->decimal('total', 11)->nullable();
            $table->unsignedBigInteger('deuda_id')->nullable();
            $table->boolean('deuda_generada')->nullable();
            $table->decimal('deuda_adelanto', 11)->nullable();
            $table->unsignedTinyInteger('id_estado')->default(3)->index('Compra_CompraEstado_fk')->comment('1=Aprobada, 2=Desestimada, 3=Espera');
            $table->date('fecha_anulacion')->nullable();
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
        Schema::dropIfExists('compras');
    }
};
