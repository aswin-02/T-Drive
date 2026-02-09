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
        Schema::create('shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('shareable_type'); // 'file' or 'folder'
            $table->unsignedBigInteger('shareable_id');
            $table->enum('access_type', ['link', 'email']);
            $table->string('token')->nullable()->unique(); // for link sharing
            $table->enum('permission', ['view', 'download', 'edit']);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // Index for polymorphic relationship
            $table->index(['shareable_type', 'shareable_id']);
            // Index for token lookups
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shares');
    }
};
