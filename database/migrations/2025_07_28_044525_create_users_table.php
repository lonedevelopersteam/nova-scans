<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**\
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('email')->unique();
            $table->text('password');
            $table->enum('role', ['Admin', 'Editor', 'Reader']);
            $table->text('device_id')->nullable();
            $table->text('access_token')->nullable();
            $table->timestamp('access_token_expire')->nullable();
            $table->timestamps();

            $table->index(['role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
