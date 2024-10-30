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
        Schema::table('book', function (Blueprint $table) {
            $table->foreign(['author_id'], 'book_ibfk_1')->references(['author_id'])->on('author')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['category_id'], 'book_ibfk_2')->references(['category_id'])->on('category')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['supplier_id'], 'book_ibfk_3')->references(['supplier_id'])->on('supplier')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book', function (Blueprint $table) {
            $table->dropForeign('book_ibfk_1');
            $table->dropForeign('book_ibfk_2');
            $table->dropForeign('book_ibfk_3');
        });
    }
};
