<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyLimitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buy_limit', function (Blueprint $table) {
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
                ->string('type');
            $table
                ->double('value');
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
        Schema::dropIfExists('buy_limit');
    }
}
