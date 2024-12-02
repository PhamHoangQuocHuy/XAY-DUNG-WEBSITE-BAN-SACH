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
        Schema::create('user', function (Blueprint $table) {
            $table->integer('user_id', true);
            $table->string('username', 50)->nullable()->unique('username');
            $table->string('email', 100)->unique('email');
            $table->string('password')->nullable();
            $table->string('google_id')->nullable();
            $table->string('fullname', 100);
            $table->string('address')->nullable();
            $table->char('phone', 10)->nullable()->unique('phone');
            $table->enum('role', ['customer', 'admin'])->default('customer');
            $table->dateTime('register_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
