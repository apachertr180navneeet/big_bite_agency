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
        Schema::create('purches_book_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purches_book_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('quantity');
            $table->string('rate');
            $table->string('tax');
            $table->string('amount');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Set foreign keys
            $table->foreign('purches_book_id')->references('id')->on('purches_books')->onDelete('set null');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purches_book_items');
    }
};
