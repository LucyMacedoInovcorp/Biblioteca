<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->datetime('data_hora');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('modulo'); // livros, autores, editoras, requisicoes, etc
            $table->unsignedBigInteger('objeto_id')->nullable(); // ID do registro afetado
            $table->text('alteracao'); // Descrição da ação realizada
            $table->ipAddress('ip');
            $table->text('browser');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['data_hora', 'user_id', 'modulo']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
};