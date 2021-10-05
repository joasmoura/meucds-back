<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownloadMusicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downloads_musicas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('musica_id');
            $table->integer('num_download')->default('0');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('mac')->nullable();
            $table->timestamps();

            $table->foreign('musica_id')
            ->references('id')
            ->on('musicas')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('downloads_musicas');
    }
}
