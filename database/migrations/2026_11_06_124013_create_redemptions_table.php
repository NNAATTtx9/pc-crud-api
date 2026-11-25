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
        Schema::create('redemption', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrant_id')->constrained('registrant');
            $table->foreignId('booth_staff_id')->constrained('booth_staff');
            $table->enum('booth_type', ['game', 'food', 'merchandise']);
            $table->integer('amount');
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->dateTime('redeemed_at')->useCurrent();
            $table->unique(['registrant_id', 'booth_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redemption');
    }
};
