<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('encomendas', function (Blueprint $table) {
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('encomendas', function (Blueprint $table) {
            $table->dropColumn(['stripe_product_id', 'stripe_price_id']);
        });
    }
};
