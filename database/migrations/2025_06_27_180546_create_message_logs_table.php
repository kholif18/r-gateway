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
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->string('client_name'); // Nama aplikasi pihak ketiga
            $table->string('session_name'); // Nama session WhatsApp
            $table->string('phone'); // Nomor tujuan
            $table->text('message'); // Isi pesan
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->text('response')->nullable(); // Response dari backend
            $table->timestamp('sent_at')->nullable(); // Waktu pengiriman (boleh null saat pending)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};
