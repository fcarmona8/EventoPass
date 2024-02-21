<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id');
            $table->string('session_code')->unique()->nullable();
            $table->timestamp('date_time');
            $table->integer('max_capacity')->nullable();
            $table->timestamp('online_sale_end_time')->nullable();
            $table->integer('ticket_quantity')->nullable();
            $table->boolean('named_tickets')->default(false); 
            $table->boolean('closed')->default(false);
            $table->timestamps();
            $table->foreign('event_id')->references('id')->on('events');
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('sessions');
    }
};
