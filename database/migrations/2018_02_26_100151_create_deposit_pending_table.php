<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositPendingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::create('deposit_pending', function (Blueprint $table) {
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
        Schema::dropIfExists('deposit_pending');
    }
}
