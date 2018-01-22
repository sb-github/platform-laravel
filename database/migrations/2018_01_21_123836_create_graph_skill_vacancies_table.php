<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGraphSkillVacanciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('graph_skill_vacancies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('graph_skill_id')->unsigned();
			$table->integer('vacancy_id')->unsigned();
			$table->datetime('last_date');
            $table->timestamps();
			
			$table->foreign('graph_skill_id')->references('id')->on('graph_skill');
            $table->foreign('vacancy_id')->references('id')->on('vacancies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('graph_skill_vacancies');
    }
}
