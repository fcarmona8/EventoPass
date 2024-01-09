<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->text('description');
            $table->string('main_image', 255);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('venue_id');
            $table->timestamp('event_date')->nullable();
            $table->integer('max_capacity')->nullable();
            $table->string('video_link', 255)->nullable();
            $table->boolean('hidden')->default(false);
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('venue_id')->references('id')->on('venues');
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
