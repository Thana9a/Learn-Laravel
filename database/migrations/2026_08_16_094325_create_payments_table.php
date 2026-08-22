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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('provider', ['cash', 'bakong_qr'])->default('cash');
            $table->decimal('amount', 10, 2);
            $table->json('meta_data')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'expired'])->default('pending');
            $table->string('transaction_id')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
