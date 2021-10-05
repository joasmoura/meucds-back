<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cds', function (Blueprint $table) {
            $table->id();
            $table->string('artista')->nullable();
            $table->string('titulo');
            $table->string('youtube')->nullable();
            $table->text('url_download')->nullable();
            $table->integer('nun_download')->nullable();
            $table->integer('num_play')->default(0);
            $table->integer('num_curtir')->default(0);
            $table->integer('num_descurtir')->default(0);
            $table->date('data_lancamento')->nullable();
            $table->boolean('lancamento')->default(0);
            $table->boolean('estourado')->default(0);
            $table->boolean('novidade')->default(0);
            $table->boolean('aovivo')->default(0);
            $table->boolean('recente')->default(0);
            $table->boolean('mp3')->default(0);
            $table->boolean('semana')->default(0);
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->unsignedBigInteger('artista_id')->nullable();
            $table->longText('texto')->nullable();
            $table->text('img')->nullable();
            $table->string('url')->nullable();
            $table->integer('ordem')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('status')->default(1);
            $table->text('motivo_deletado')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('categoria_id')
            ->references('id')
            ->on('categorias');

            $table->foreign('artista_id')
            ->references('id')
            ->on('artistas');

            $table->foreign('user_id')
            ->references('id')
            ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cds');
    }
}
