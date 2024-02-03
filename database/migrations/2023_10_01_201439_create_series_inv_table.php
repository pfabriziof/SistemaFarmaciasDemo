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
        Schema::create('series_inv', function (Blueprint $table) {
            $table->increments('id_serie');
            $table->unsignedInteger('id_sucursal')->index('SerieInv_Sucursal_fk');
            $table->unsignedTinyInteger('id_tipo_comprobante')->index('SerieInv_TipoComprobante_fk');
            $table->string('serie', 45)->nullable();
            $table->unsignedTinyInteger('estado')->default(1);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('series_inv');
    }
};
