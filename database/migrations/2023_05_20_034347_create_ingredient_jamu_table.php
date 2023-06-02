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
        Schema::create('ingredient_jamu', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ingredient_id')->unsigned()->nullable();
            $table->foreign('ingredient_id')->references('id')->on('ingredients')->onUpdate('cascade')
                ->onDelete('set null');
            $table->integer('jamu_id')->unsigned()->nullable();
            $table->foreign('jamu_id')->references('id')->on('jamu')->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingredient_jamus');
    }
};
