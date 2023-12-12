<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['name' => 'client'],
            ['name' => 'administrator']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Eliminación de la tabla roles
        Schema::dropIfExists('roles');
    }
};
