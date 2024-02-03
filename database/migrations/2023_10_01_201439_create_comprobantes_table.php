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
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->bigIncrements('id_comprobante');
            $table->unsignedBigInteger('id_cliente')->nullable()->index('Comprobante_Cliente_fk');
            $table->unsignedBigInteger('id_usuario')->nullable()->index('Comprobante_Usuario_fk');
            $table->unsignedInteger('id_sucursal')->nullable()->index('Comprobante_Sucursal_fk');
            $table->unsignedTinyInteger('id_tipo_comprobante')->nullable()->index('Comprobante_TipoComprobante_fk');
            $table->unsignedTinyInteger('id_moneda')->nullable()->index('Comprobante_Moneda_fk');
            $table->unsignedTinyInteger('id_estado_comprobante')->nullable()->index('Comprobante_Estado_fk');
            $table->unsignedTinyInteger('id_medio_pago')->nullable()->index('Comprobante_MedioPago_fk');
            $table->unsignedTinyInteger('id_tipo_cambio')->nullable()->index('Comprobante_TipoCambio_fk');
            $table->unsignedInteger('id_serie')->nullable()->index('Comprobante_Serie_fk');
            $table->bigInteger('correlativo')->nullable();
            $table->string('nombreCliente', 250)->nullable();
            $table->string('nroDocCliente', 45)->nullable();
            $table->string('direccionCliente', 250)->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->text('comentario')->nullable();
            $table->decimal('op_inafectas', 11)->nullable();
            $table->decimal('op_exoneradas', 11)->nullable();
            $table->decimal('op_gravadas', 11)->nullable();
            $table->decimal('icbper', 11)->nullable();
            $table->decimal('porcentaje_igv', 11)->nullable();
            $table->decimal('igv', 11)->nullable();
            $table->decimal('total', 11)->nullable();
            $table->string('external_id', 250)->nullable();
            $table->string('formato_impresion', 45)->nullable();
            $table->date('fecha_anulacion')->nullable();
            $table->text('motivo_anulacion')->nullable();
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
        Schema::dropIfExists('comprobantes');
    }
};
