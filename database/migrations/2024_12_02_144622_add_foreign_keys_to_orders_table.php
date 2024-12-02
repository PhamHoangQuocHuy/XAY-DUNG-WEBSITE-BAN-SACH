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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign(['user_id'], 'orders_ibfk_2')->references(['user_id'])->on('user')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['shipping_id'], 'orders_ibfk_3')->references(['shipping_id'])->on('shipping')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['coupon_id'], 'orders_ibfk_4')->references(['coupon_id'])->on('coupons')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['payment_id'], 'orders_ibfk_5')->references(['payment_id'])->on('payment')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_ibfk_2');
            $table->dropForeign('orders_ibfk_3');
            $table->dropForeign('orders_ibfk_4');
            $table->dropForeign('orders_ibfk_5');
        });
    }
};
