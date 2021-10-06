<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicidades', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->enum('origem',['local','youtube']);
            $table->string('src')->nullable();
            $table->text('link')->nullable();
            $table->unsignedBigInteger('cd_id');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('cd_id')
            ->references('id')
            ->on('cds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publicidades');
    }
}
