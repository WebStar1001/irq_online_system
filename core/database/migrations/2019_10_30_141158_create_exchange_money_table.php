<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_money', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->integer('from_currency_id')->nullable();
            $table->decimal('from_currency_amount',18,8)->default(0);
            $table->decimal('from_currency_charge',18,8)->default(0);
            $table->integer('to_currency_id')->nullable();
            $table->decimal('to_currency_amount',18,8)->default(0);
            $table->string('trx')->nullable();
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('exchange_money');
    }
}
