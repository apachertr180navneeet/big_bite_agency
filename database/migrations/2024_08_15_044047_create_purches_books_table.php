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
        Schema::create('purches_books', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->string('invoice_number');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('transport');
            $table->decimal('total_tax', $precision = 8, $scale = 2);
            $table->decimal('other_expense', $precision = 8, $scale = 2);
            $table->decimal('discount', $precision = 8, $scale = 2);
            $table->decimal('round_off', $precision = 8, $scale = 2);
            $table->decimal('grand_total', $precision = 8, $scale = 2);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Adding the foreign key constraint on vendor_id
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purches_books');
    }
};
