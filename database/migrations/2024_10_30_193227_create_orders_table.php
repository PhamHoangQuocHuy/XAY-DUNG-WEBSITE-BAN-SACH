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
            $table->string('username', 50);
            $table->integer('book_id')->index('book_id');
            $table->string('book_name');
            $table->string('code_order', 20)->unique('code_order');
            $table->dateTime('order_date');
            $table->string('shipping_address');
            $table->enum('status', ['Processing', 'Delivered', 'Cancelled'])->default('Processing');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('price');
            $table->unsignedInteger('totalprice');
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
