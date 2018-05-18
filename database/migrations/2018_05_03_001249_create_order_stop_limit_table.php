<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderStopLimitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_stop_limit', function (Blueprint $table) {
            $table->increments('id');
            $table
                ->integer('user_id')
                ->unsigned();
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
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
                ->double('stop');
            $table
                ->double('price');
            $table
                ->double('amount');
            $table
                ->double('value');
            $table
                ->double('fee');
            $table
                ->double('total');
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
        Schema::dropIfExists('order_stop_limit');
    }
}
