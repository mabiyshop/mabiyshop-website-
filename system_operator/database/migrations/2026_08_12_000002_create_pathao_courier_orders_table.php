<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePathaoCourierOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('pathao_courier_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('seller_id');
            $table->string('merchant_order_id', 100)->unique();
            $table->string('state', 20)->default('in_progress');
            $table->string('consignment_id', 100)->nullable();
            $table->string('last_error', 255)->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'seller_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pathao_courier_orders');
    }
}
