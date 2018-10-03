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

        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->string('name');
            $table->string('coupon');
            $table->float('percent')->nullable();
            $table->float('price')->nullable();
            $table->integer('use_max')->nullable();
            $table->float('amount_min')->nullable();
            $table->timestamp('begin')->nullable();
            $table->timestamp('end')->nullable();
            $table->timestamps();
        });

        Schema::create('coupon_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->integer('coupon_id')->unsigned();
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->timestamp('use')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('coupon_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->integer('coupon_id')->unsigned();
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->string('token');
            $table->string('reference');

            $table->float('price_ht');
            $table->float('vat_price');
            $table->float('vat_percent');
            $table->string('shipping_order_id')->nullable();
            $table->string('shipping_name');
            $table->string('shipping_delay');
            $table->string('shipping_url')->nullable();
            $table->float('shipping_price');
            $table->float('price_ttc');
            $table->integer('total_quantity');

            $table->integer('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string("user_firstname");
            $table->string("user_lastname");
            $table->string("user_email");

            $table->string('delivery_gender');
            $table->string('delivery_firstname');
            $table->string('delivery_lastname');
            $table->string('delivery_address');
            $table->string('delivery_additional_address')->nullable();
            $table->string('delivery_zip_code');
            $table->string('delivery_city');
            $table->integer('delivery_country_id');
            $table->string('delivery_country_name');
            $table->string('delivery_phone');

            $table->string('billing_gender');
            $table->string('billing_firstname');
            $table->string('billing_lastname');
            $table->string('billing_address');
            $table->string('billing_additional_address')->nullable();
            $table->string('billing_zip_code');
            $table->string('billing_city');
            $table->integer('billing_country_id');
            $table->string('billing_country_name');
            $table->string('billing_phone');

            $table->integer('coupon_id')->nullable()->unsigned();
            $table->foreign('coupon_id')->references('id')->on('coupons');
            $table->float('coupon_price')->nullable();
            $table->string('coupon_name')->nullable();

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
