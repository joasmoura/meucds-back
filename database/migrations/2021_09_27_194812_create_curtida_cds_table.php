<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurtidaCdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curtida_cds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cd_id');
            $table->integer('num_curtida')->default('0');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('mac')->nullable();
            $table->timestamps();

            $table->foreign('cd_id')
            ->references('id')
            ->on('cds')
            ->onDelete('cascade');

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
        Schema::dropIfExists('curtida_cds');
    }
}
