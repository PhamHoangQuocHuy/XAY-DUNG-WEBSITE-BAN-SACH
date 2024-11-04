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
        Schema::create('orders', function (Blueprint $table) {
            $table->integer('order_id', true);
            $table->integer('user_id')->index('user_id');
            $table->integer('payment_id')->index('book_id');
            $table->integer('shipping_id')->index('shipping_id');
            $table->string('code_order', 20)->unique('code_order');
            $table->dateTime('order_date');
            $table->enum('order_status', ['Processing', 'Delivered', 'Cancelled'])->default('Processing');
            $table->unsignedInteger('order_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
