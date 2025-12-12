<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->dateTime('scheduled_at');
            $table->dateTime('end_time');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['stripe', 'transfer', 'cash'])->nullable();
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->string('google_event_id')->nullable();
            $table->json('participants_data')->nullable(); // For group/couples data
            $table->string('token')->unique()->nullable(); // For secure links
            $table->timestamp('expires_at')->nullable(); // For 24h auto-cancellation
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
