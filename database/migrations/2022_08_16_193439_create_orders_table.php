<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid()->unique();
            $table->uuid('user_uuid');
            $table->uuid('order_status_uuid');
            $table->uuid('payment_uuid')->nullable();
            $table->json('products');
            $table->json('address');
            $table->float('delivery_fee');
            $table->float('amount');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->index(['uuid']);
            $table->foreign('user_uuid')->references('uuid')->on('users');
            $table->foreign('order_status_uuid')->references('uuid')->on('order_statuses');
            $table->foreign('payment_uuid')->references('uuid')->on('payments');
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
};
