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
        Schema::create('receipt_book_vouchers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('receipt_vouchers_number');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->decimal('amount', 8, 2);
            $table->decimal('discount', 8, 2)->default(0.00);
            $table->decimal('round_off', 8, 2)->default(0.00);
            $table->decimal('grand_total', 8, 2);
            $table->text('remark');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_book_vouchers');
    }
};

