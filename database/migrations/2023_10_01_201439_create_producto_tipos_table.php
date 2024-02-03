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
        Schema::create('producto_tipos', function (Blueprint $table) {
            $table->tinyIncrements('id_producto_tipo');
            $table->string('tipo', 250)->nullable();
            $table->tinyInteger('impuesto')->nullable()->comment('1=Gravado, 2=inafecto, 3= exonerado,
4=icbper');
            $table->decimal('icbper', 11)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto_tipos');
    }
};
