<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id');
            $table->string('status');
            $table->integer('status_code');
            $table->bigInteger('total');
            $table->json('product_detail');
            $table->string('order_number')->nullable();
            $table->string('order_id')->nullable();
            $table->dateTime('ordered_at')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_id')->nullable();
            $table->dateTime('invoiced_at')->nullable();
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
        Schema::dropIfExists('order_history');
    }
}


