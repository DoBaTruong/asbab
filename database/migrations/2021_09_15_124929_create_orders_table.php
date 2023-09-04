<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('mail');
            $table->string('address');
            $table->integer('user_id')->nullable();
            $table->string('code');
            $table->integer('status')->default(0);
            $table->integer('ship_id')->nullable();
            $table->integer('coupon_id')->nullable();
            $table->float('fee_ship', 4, 2);
            $table->float('amount', 11, 2);
            $table->integer('paymethod');
            $table->string('note', 500)->nullable();
            $table->string('reason', 500)->nullable();
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
        Schema::dropIfExists('orders');
    }
}
