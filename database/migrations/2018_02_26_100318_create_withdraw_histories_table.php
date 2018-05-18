<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::create('withdraw_histories', function (Blueprint $table) {
            $table
                ->increments('id');
            $table
                ->integer('coin_id')
                ->unsigned();
            $table
                ->foreign('coin_id')
                ->references('id')
                ->on('coins')
                ->onDelete('cascade');
            $table
                ->integer('user_id')
                ->unsigned();
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table
                ->string('address');
            $table
                ->double('amount');
            $table
                ->string('txid');
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
        Schema::dropIfExists('withdraw_histories');
    }
}
