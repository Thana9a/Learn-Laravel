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
        Schema::create('discount_scopes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->morphs('scopeable');
            $table->foreignId('discount_id')->constrained('discounts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_scopes');
    }
};
