<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_markets', function (Blueprint $table) {
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
                ->double('last_price');
            $table
                ->double('change');
            $table
                ->double('volume');
            $table
                ->double('high');
            $table
                ->double('low');
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
        Schema::dropIfExists('data_markets');
    }
}
