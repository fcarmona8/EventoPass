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
            $table->unsignedBigInteger('main_image_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('venue_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('event_date')->nullable();
            $table->string('video_link', 255)->nullable();
            $table->boolean('hidden')->default(false);
            $table->boolean('nominal')->default(false);
            $table->timestamps();

            // Define las relaciones con otras tablas
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('venue_id')->references('id')->on('venues');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
