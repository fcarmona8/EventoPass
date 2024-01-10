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
            $table->string('name', 255);
            $table->text('location');
            $table->integer('capacity')->nullable();
            $table->timestamps();
        });        
    }

    public function down()
    {
        Schema::dropIfExists('venues');
    }
};
