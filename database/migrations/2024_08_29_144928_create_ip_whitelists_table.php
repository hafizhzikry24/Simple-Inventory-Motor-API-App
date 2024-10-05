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
        Schema::create('ip_whitelists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ip_address')->unique();
            $table->string('description')->nullable();
            $table->foreignUuid('office_id')->constrained('offices')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_whitelists');
    }
};
