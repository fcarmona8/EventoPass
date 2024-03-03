<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentariosTable extends Migration
{
    public function up()
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id');
            $table->string('nombre');
            $table->integer('smileyRating')->nullable();
            $table->integer('puntuacion');
            $table->string('titulo');
            $table->text('comentario');
            $table->timestamps();
            $table->foreign('event_id')->references('id')->on('events');
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('comentarios');
    }
};
