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
        Schema::table('series_inv', function (Blueprint $table) {
            $table->foreign(['id_sucursal'], 'SerieInv_Sucursal_fk')->references(['id_sucursal'])->on('sucursales');
            $table->foreign(['id_tipo_comprobante'], 'SerieInv_TipoComprobante_fk')->references(['id_tipo_comprobante'])->on('tipos_comprobante');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('series_inv', function (Blueprint $table) {
            $table->dropForeign('SerieInv_Sucursal_fk');
            $table->dropForeign('SerieInv_TipoComprobante_fk');
        });
    }
};
