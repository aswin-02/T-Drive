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
        Schema::create('action_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refer_id')->nullable(); // ID of the affected model
            $table->string('model')->nullable();               // Model name
            $table->string('action');                          // create, update, delete
            $table->json('old_data')->nullable();              // Old data (before update)
            $table->json('new_data')->nullable();              // New data (after create/update)
            $table->ipAddress('ip')->nullable();               // IP address of user
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();         // ID of the user who did it
            $table->timestamps();       // When the log was recorded
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_logs');
    }
};
