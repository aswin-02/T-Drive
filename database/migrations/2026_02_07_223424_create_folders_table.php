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
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('parent_id')->nullable(); // for nested folders
            $table->string('name');
            $table->string('path'); // storage path reference
            $table->timestamps(); // created_at and updated_at

            // Indexes for better performance
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('created_at');

            // Foreign key for parent folder (self-referencing)
            $table->foreign('parent_id')->references('id')->on('folders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
