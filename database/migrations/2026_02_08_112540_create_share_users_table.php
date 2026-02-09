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
        Schema::create('share_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('share_id')->constrained('shares')->onDelete('cascade');
            $table->string('email');
            $table->timestamp('created_at')->nullable();

            // Index for faster lookups by share_id
            $table->index('share_id');
            // Index for email lookups
            $table->index('email');
            // Composite index for unique email per share
            $table->unique(['share_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_users');
    }
};
