<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpressPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('express_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('payto_id')->nullable()->comment = 'merchant_id';
            $table->integer('paidby_id')->nullable()->comment = 'user_id';
            $table->integer('wallet_id')->nullable();
            $table->string('transaction')->nullable();
            $table->integer('status')->default(0);
            $table->text('all_data')->nullable()->comment = 'amount, currency,  public_key, custom, details, ipn_url, success_url, cancel_url  ';
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
        Schema::dropIfExists('express_payments');
    }
}
