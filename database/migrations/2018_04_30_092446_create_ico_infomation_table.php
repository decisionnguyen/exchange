<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIcoInfomationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ico_infomation', function (Blueprint $table) {
            $table->increments('id');
            $table
                ->integer('market_id')
                ->unsigned();
            $table
                ->foreign('market_id')
                ->references('id')
                ->on('markets')
                ->onDelete('cascade');
            $table
                ->string('name');
            $table
                ->string('logo');
            $table
                ->string('color');
            $table
                ->string('description');
            $table
                ->dateTime('start_date');
            $table
                ->dateTime('end_date');
            $table
                ->string('website');
            $table
                ->string('facebook');
            $table
                ->string('twitter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ico_infomation');
    }
}
