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
        Schema::create('sales_books', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->string('dispatch_number');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('item_weight');
            $table->decimal('total_tax', $precision = 8, $scale = 2);
            $table->decimal('other_expense', $precision = 8, $scale = 2);
            $table->decimal('discount', $precision = 8, $scale = 2);
            $table->decimal('round_off', $precision = 8, $scale = 2);
            $table->decimal('grand_total', $precision = 8, $scale = 2);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Adding the foreign key constraint on customer_id
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('set null');

            // Adding the foreign key constraint on company_id
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_books');
    }
};
