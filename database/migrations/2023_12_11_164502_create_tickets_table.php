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
            $table->boolean('is_validated')->default(false);
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('session_id');
            $table->string('name',255)->nullable();
            $table->string('dni',255)->nullable();
            $table->string('telefono',255)->nullable();
            $table->string('unicIdTicket',255)->nullable();
            $table->string('buyerName',255)->nullable();
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
