<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table
                ->increments('id');
            $table
                ->integer('market_id')
                ->unsigned();
            $table
                ->foreign('market_id')
                ->references('id')
                ->on('markets')
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
                ->string('type');
            $table
                ->double('value');
            $table
                ->double('price');
            $table
                ->double('total');
            $table
                ->double('fee');
            $table
                ->double('total_fee');       
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
        Schema::dropIfExists('orders');
    }
}
