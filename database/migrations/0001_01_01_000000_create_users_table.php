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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile', 20)->unique()->nullable();
            $table->enum('user_type', ['user', 'admin'])->default('user');
            $table->foreignId('role_id')->nullable();
            $table->text('image')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Others'])->default('Male');
            $table->string('address', 255)->nullable();
            $table->foreignId('state_id')->nullable();
            $table->foreignId('district_id')->nullable();
            $table->foreignId('city_id')->nullable();
            $table->foreignId('pincode_id')->nullable();
            $table->string('password');
            $table->text('password1')->nullable();
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_show')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
