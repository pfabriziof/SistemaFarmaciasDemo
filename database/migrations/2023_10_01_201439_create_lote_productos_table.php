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
        Schema::create('lote_productos', function (Blueprint $table) {
            $table->bigIncrements('id_lote');
            $table->unsignedInteger('id_sucursal')->index('Lote_Sucursal_fk');
            $table->unsignedBigInteger('id_producto')->index('Lote_Producto_fk');
            $table->string('lote', 250);
            $table->integer('cantidad');
            $table->date('fecha_expiracion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lote_productos');
    }
};
