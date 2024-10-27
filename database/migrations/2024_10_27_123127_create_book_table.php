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
        Schema::create('book', function (Blueprint $table) {
            $table->integer('book_id', true);
            $table->string('book_name');
            $table->char('isbn', 13)->unique('isbn');
            $table->integer('author_id')->index('author_id');
            $table->integer('category_id')->index('category_id');
            $table->string('publisher');
            $table->date('publication _date');
            $table->integer('quantity');
            $table->decimal('price', 10, 0)->unsigned();
            $table->text('description');
            $table->string('image');
            $table->string('language', 50);
            $table->string('tags');
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book');
    }
};
