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
        Schema::create('run_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event');
            $table->string('slug');
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('price');
            $table->integer('credits')->default(60);
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('run_category');
    }
};
