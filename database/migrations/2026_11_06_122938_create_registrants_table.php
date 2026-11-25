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
        Schema::create('registrant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event');
            $table->foreignId('category_id')->constrained('run_category');
            $table->string('name');
            $table->string('email');
            $table->string('mobile', 32);
            $table->enum('tshirt_size', ['XS', 'S', 'M', 'L', 'XL', 'XXL']);
            $table->string('organisation')->nullable();
            $table->integer('donation_amount');
            $table->char('qr_code', 6)->unique();
            $table->integer('credit_balance')->default(60);
            $table->enum('wallet_status', ['active', 'exhausted'])->default('active');
            $table->enum('goodie_status', ['pending', 'collected'])->default('pending');
            $table->boolean('pdpa_consent')->default(false);
            $table->dateTime('registered_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrant');
    }
};
