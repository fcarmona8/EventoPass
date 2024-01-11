<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('type_id');
            $table->timestamps();
            $table->foreign('purchase_id')->references('id')->on('purchases');
            $table->foreign('type_id')->references('id')->on('ticket_types');
            $table->foreign('session_id')->references('id')->on('sessions');
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
