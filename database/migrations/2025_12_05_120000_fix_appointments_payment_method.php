<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create new table with updated schema
        Schema::create('appointments_v2', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->dateTime('scheduled_at');
            $table->dateTime('end_time');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            // Added 'paypal' to the enum
            $table->enum('payment_method', ['stripe', 'transfer', 'cash', 'paypal'])->nullable();
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('google_event_id')->nullable();
            $table->json('participants_data')->nullable();
            $table->string('token')->unique()->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->timestamps();
        });

        // 2. Copy data from old table to new table
        DB::statement('INSERT INTO appointments_v2 (
            id, folio, user_id, service_id, scheduled_at, end_time, status, 
            payment_method, payment_status, notes, google_event_id, participants_data, 
            token, expires_at, customer_name, customer_email, customer_phone, 
            created_at, updated_at
        ) SELECT 
            id, folio, user_id, service_id, scheduled_at, end_time, status, 
            payment_method, payment_status, notes, google_event_id, participants_data, 
            token, expires_at, customer_name, customer_email, customer_phone, 
            created_at, updated_at
        FROM appointments');

        // 3. Drop old table
        Schema::drop('appointments');

        // 4. Rename new table
        Schema::rename('appointments_v2', 'appointments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive migration for SQLite fixes, 
        // down method is complex to write perfectly without data loss awareness 
        // (need to recreate old schema without paypal).
        // For now, leaving empty or throwing exception is acceptable for this scratchpad fix.
    }
};
