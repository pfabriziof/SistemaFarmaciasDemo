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
        Schema::table('deudas_compras', function (Blueprint $table) {
            $table->foreign(['id_compra'], 'DeudaCompra_Compra_fk')->references(['id_compra'])->on('compras');
            $table->foreign(['id_proveedor'], 'DeudaCompra_Proveedor_fk')->references(['id_proveedor'])->on('proveedores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deudas_compras', function (Blueprint $table) {
            $table->dropForeign('DeudaCompra_Compra_fk');
            $table->dropForeign('DeudaCompra_Proveedor_fk');
        });
    }
};
