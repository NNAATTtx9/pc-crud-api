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
        Schema::create('api_token', function (Blueprint $table) {
            $table->char('token', 60)->primary();
            $table->enum('owner_type', ['admin', 'booth']);
            $table->unsignedBigInteger('owner_id');
            $table->dateTime('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_token');
    }
};
