<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferenceAndUserInformationToShoppingcartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoppingcart', function (Blueprint $table) {
            $table->string('token');

            $table->string('shipping_name');
            $table->string('shipping_delay');
            $table->string('shipping_url')->nullable();
            $table->float('shipping_price');

            $table->integer('user_id')->nullable()->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete("cascade");
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

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
