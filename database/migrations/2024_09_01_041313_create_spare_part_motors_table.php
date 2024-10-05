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
        Schema::create('spare_part_motors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description');
            $table->decimal('price');
            $table->foreignUuid('motor_id')->constrained('motors')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_part_motors');
    }
};


