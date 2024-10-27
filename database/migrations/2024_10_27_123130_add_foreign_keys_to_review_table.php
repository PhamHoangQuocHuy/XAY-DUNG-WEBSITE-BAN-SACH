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
        Schema::table('review', function (Blueprint $table) {
            $table->foreign(['book_id'], 'review_ibfk_1')->references(['book_id'])->on('book')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'], 'review_ibfk_2')->references(['user_id'])->on('user')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('review', function (Blueprint $table) {
            $table->dropForeign('review_ibfk_1');
            $table->dropForeign('review_ibfk_2');
        });
    }
};
