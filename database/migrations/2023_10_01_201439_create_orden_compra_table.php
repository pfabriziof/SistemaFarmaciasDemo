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
        Schema::create('orden_compra', function (Blueprint $table) {
            $table->bigIncrements('id_orden_compra');
            $table->unsignedBigInteger('id_usuario')->nullable()->index('OrdenCompra_Usuario_fk');
            $table->unsignedInteger('id_sucursal')->nullable()->index('OrdenCompra_Sucursal_fk');
            $table->unsignedBigInteger('id_proveedor')->index('OrdenCompra_Proveedor_fk');
            $table->unsignedTinyInteger('id_moneda')->index('OrdenCompra_Moneda_fk');
            $table->unsignedTinyInteger('id_medio_pago')->index('OrdenCompra_MedioPago_fk');
            $table->unsignedTinyInteger('id_tipo_cambio')->index('OrdenCompra_TipoCambio_fk');
            $table->string('email', 250)->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->bigInteger('numeracion')->nullable();
            $table->decimal('op_inafectas', 11)->nullable();
            $table->decimal('op_exoneradas', 11)->nullable();
            $table->decimal('op_gravadas', 11)->nullable();
            $table->decimal('icbper', 11)->nullable();
            $table->decimal('porcentaje_igv', 11)->nullable();
            $table->decimal('igv', 11)->nullable();
            $table->decimal('total', 11)->nullable();
            $table->tinyInteger('estado')->default(3)->comment('1=Aprobada, 2=Desestimada, 3=Espera');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orden_compra');
    }
};
