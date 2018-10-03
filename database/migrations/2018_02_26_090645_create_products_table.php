<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Table produit */
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->boolean('published');
            $table->boolean('week_selection')->nullable()->default(0);
            $table->text('name');
            $table->text('slug');
            $table->text('summary')->nullable();
            $table->text('description');
            $table->text('image')->nullable();
            $table->text("imageList")->nullable();
            $table->integer('shop_category_id')->unsigned();
            $table->integer('order')->nullable()->default(0);
            $table->string('reference');
            $table->string('isbn_code')->nullable();
            $table->string('ean_code')->nullable();
            $table->string('upc_code')->nullable();
            $table->integer('vat_id')->nullable();
            $table->float('price_ht');
            $table->float('price_ttc');
            $table->dateTime('reduce_date_begin')->nullable();
            $table->dateTime('reduce_date_end')->nullable();
            $table->float('reduce_price')->nullable();
            $table->float('reduce_percent')->nullable();
            $table->integer('stock_brut')->nullable();
            $table->integer('stock_booked')->nullable();
            $table->integer('stock_available')->nullable();
            $table->float('weight')->nullable();
            $table->float('height')->nullable();
            $table->float('length')->nullable();
            $table->float('width')->nullable();
            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->timestamps();
            $table->foreign('shop_category_id')->references('id')->on('shop_categories');
        });

        Schema::create('products_has_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('shop_category_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete("cascade");
            $table->foreign('shop_category_id')->references('id')->on('shop_categories')->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
