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
        Schema::table('payment', function (Blueprint $table) {
            $table->foreign(['order_id'], 'payment_ibfk_1')->references(['order_id'])->on('orders')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'], 'payment_ibfk_2')->references(['user_id'])->on('user')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            $table->dropForeign('payment_ibfk_1');
            $table->dropForeign('payment_ibfk_2');
        });
    }
};
