<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('encomendas', function (Blueprint $table) {
            $table->string('numero', 10)->nullable();
            $table->string('porta', 10)->nullable();
            $table->string('localidade', 100);
            $table->string('codigo_postal', 12);
            $table->string('concelho', 100);
            $table->string('pais', 56);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encomendas', function (Blueprint $table) {
            //
        });
    }
};
