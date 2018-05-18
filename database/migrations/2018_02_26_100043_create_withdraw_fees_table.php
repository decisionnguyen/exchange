<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
       public function up()
    {
        Schema::create('withdraw_fees', function (Blueprint $table) {
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
        Schema::dropIfExists('withdraw_fees');
    }
}
