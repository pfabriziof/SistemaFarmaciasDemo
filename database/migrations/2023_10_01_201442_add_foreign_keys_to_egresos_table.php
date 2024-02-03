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
        Schema::table('egresos', function (Blueprint $table) {
            $table->foreign(['id_compra'], 'Egreso_Compra_fk')->references(['id_compra'])->on('compras');
            $table->foreign(['id_motivo_egreso'], 'Egreso_MotivoEgreso_fk')->references(['id_egreso_motivo'])->on('egreso_motivos');
            $table->foreign(['id_sucursal'], 'Egreso_Sucursal_fk')->references(['id_sucursal'])->on('sucursales');
            $table->foreign(['id_tipo_egreso'], 'Egreso_TipoEgreso_fk')->references(['id_tipo_egreso'])->on('tipos_egreso');
            $table->foreign(['id_usuario'], 'Egreso_Usuario_fk')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('egresos', function (Blueprint $table) {
            $table->dropForeign('Egreso_Compra_fk');
            $table->dropForeign('Egreso_MotivoEgreso_fk');
            $table->dropForeign('Egreso_Sucursal_fk');
            $table->dropForeign('Egreso_TipoEgreso_fk');
            $table->dropForeign('Egreso_Usuario_fk');
        });
    }
};
