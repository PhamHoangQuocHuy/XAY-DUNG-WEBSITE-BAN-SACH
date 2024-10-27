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
        Schema::create('payment', function (Blueprint $table) {
            $table->integer('payment_id', true);
            $table->integer('order_id')->index('order_id');
            $table->integer('user_id')->index('user_id');
            $table->string('username', 50);
            $table->enum('payment_method', ['Online Payment', 'Cash on Delivery']);
            $table->dateTime('payment_date');
            $table->enum('payment_status', ['Pending', 'Completed', 'Failed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
