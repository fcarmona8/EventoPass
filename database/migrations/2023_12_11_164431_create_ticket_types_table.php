<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketTypesTable extends Migration
{
    public function up()
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->decimal('price', 8, 2);
            $table->integer('available_tickets')->nullable();
            $table->timestamps();
        });     
      
    }

    public function down()
    {
        Schema::dropIfExists('ticket_types');
    }
};
