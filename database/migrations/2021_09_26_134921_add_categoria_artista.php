<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoriaArtista extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('artistas', function (Blueprint $table) {
            $table->unsignedBigInteger('categoria_id')->nullable();

            $table->foreign('categoria_id')
            ->references('id')
            ->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('artistas', function (Blueprint $table) {
            $table->dropColumn('categoria_id');
        });
    }
}
