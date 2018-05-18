<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markets', function (Blueprint $table) {
            $table
                ->increments('id');
            $table
                ->integer('coin_id_first')
                ->unsigned();
            $table
                ->foreign('coin_id_first')
                ->references('id')
                ->on('coins')
                ->onDelete('cascade');
            $table
                ->integer('coin_id_second')
                ->unsigned();
            $table
                ->foreign('coin_id_second')
                ->references('id')
                ->on('coins')
                ->onDelete('cascade');    
            $table
                ->string('name');
            $table
                ->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('markets');
    }
}
