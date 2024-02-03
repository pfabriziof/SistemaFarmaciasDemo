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
        Schema::table('proveedores', function (Blueprint $table) {
            $table->foreign(['id_sucursal'], 'Proveedor_Sucursal_fk')->references(['id_sucursal'])->on('sucursales');
            $table->foreign(['id_tipo_doc'], 'Proveedor_TipoDocumento_fk')->references(['id_tipo_doc'])->on('tipo_documento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropForeign('Proveedor_Sucursal_fk');
            $table->dropForeign('Proveedor_TipoDocumento_fk');
        });
    }
};
