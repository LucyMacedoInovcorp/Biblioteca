<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('livro_id');
            $table->unsignedBigInteger('requisicao_id');
            $table->text('review');
            $table->unsignedTinyInteger('rating');
            $table->string('status')->default('pendente'); // pendente, aprovada, rejeitada
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('livro_id')->references('id')->on('livros')->onDelete('cascade');
            $table->foreign('requisicao_id')->references('id')->on('requisicoes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avaliacoes');
    }
};
