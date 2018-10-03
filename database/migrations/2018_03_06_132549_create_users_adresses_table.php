<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersAdressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_adresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string("uuid");
            $table->boolean('delivery')->default(0)->nullable();
            $table->boolean('billing')->default(0)->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('gender', ['M', 'Mme'])->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('address');
            $table->string('additional_address')->nullable();
            $table->string('zip_code');
            $table->string('city');
            $table->string('phone');
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
        Schema::dropIfExists('users_adresses');
    }
}
