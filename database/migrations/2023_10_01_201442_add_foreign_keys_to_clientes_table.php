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
        Schema::table('clientes', function (Blueprint $table) {
            $table->foreign(['id_sucursal'], 'Cliente_Sucursal_fk')->references(['id_sucursal'])->on('sucursales')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['id_tipo_doc'], 'Cliente_TipoDocumento_fk')->references(['id_tipo_doc'])->on('tipo_documento')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign('Cliente_Sucursal_fk');
            $table->dropForeign('Cliente_TipoDocumento_fk');
        });
    }
};
