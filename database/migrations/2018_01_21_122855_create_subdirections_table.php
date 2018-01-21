<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubdirectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subdirections', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('direction_id')->unsigned();
			$table->integer('sub_id')->unsigned();
            $table->timestamps();
			
			$table->foreign('direction_id')->references('id')->on('directions');
            $table->foreign('sub_id')->references('id')->on('directions');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subdirections');
    }
}
