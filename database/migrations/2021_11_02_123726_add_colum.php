<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cds', function (Blueprint $table) {
            $table->date('data_publicacao')->nullable();
            $table->time('hora_publicacao')->nullable();
            $table->enum('publicacao', ['S', 'P'])->nullable();
            $table->enum('tipo_publicacao', ['PL', 'PR', 'NL'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cds', function (Blueprint $table) {
            $table->dropColumn(['data_publicacao', 'hora_publicacao', 'publicacao', 'tipo_publicacao']);
        });
    }
}
