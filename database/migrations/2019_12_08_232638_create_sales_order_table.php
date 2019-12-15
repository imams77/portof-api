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
            $table->bigIncrements('id');
            $table->string('full_name');
            $table->string('user_id');
            $table->string('email');
            $table->string('product_id');
            $table->string('product_name');
            $table->string('order_number');
            $table->string('phone_number');
            $table->string('price');
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
