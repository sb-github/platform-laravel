<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirectionSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direction_skill', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('direction_id')->unsigned();
			$table->integer('skill_id')->unsigned();
            $table->timestamps();
			
			$table->foreign('direction_id')->references('id')->on('directions');
            $table->foreign('skill_id')->references('id')->on('skills');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('direction_skill');
    }
}
