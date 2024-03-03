<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVenuesTable extends Migration
{
    public function up()
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('province', 255);
            $table->string('city', 255);
            $table->string('postal_code', 255);
            $table->string('venue_name', 255);
            $table->integer('capacity');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });        
    }

    public function down()
    {
        Schema::dropIfExists('venues');
    }
};

