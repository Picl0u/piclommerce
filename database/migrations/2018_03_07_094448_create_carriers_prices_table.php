<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarriersPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carriers_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->integer('carriers_id')->unsigned();
            $table->foreign('carriers_id')->references('id')->on('carriers')->onDelete("cascade");
            $table->integer('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete("cascade");
            $table->float('default_price')->nullable();
            $table->float("price_min");
            $table->float("price_max");
            $table->float("price");
            $table->integer("key");
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
        Schema::dropIfExists('carriers_prices');
    }
}
