<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawLimitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_limit', function (Blueprint $table) {
            $table->increments('id');
            $table
                ->integer('coin_id')
                ->unsigned();
            $table
                ->foreign('coin_id')
                ->references('id')
                ->on('coins')
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
        Schema::dropIfExists('withdraw_limit');
    }
}
