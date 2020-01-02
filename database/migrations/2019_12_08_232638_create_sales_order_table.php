<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_order', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name');
            $table->string('user_id');
            $table->string('email');
            $table->string('product_id');
            $table->string('product_name');
            $table->string('order_number');
            $table->string('phone_number');
            $table->bigInteger('price');
            $table->bigInteger('unique_price');
            $table->bigInteger('tax');
            $table->bigInteger('total');
            $table->dateTime('expired_at');
            $table->string('download_url');
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
        Schema::dropIfExists('sales_order');
    }
}
