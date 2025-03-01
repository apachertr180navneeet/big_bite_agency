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
        Schema::table('receipt_book_vouchers', function (Blueprint $table) {
            $table->enum('payment_type', ['cash', 'cheque', 'online bank', 'other'])->default('cash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_book_vouchers', function (Blueprint $table) {
            table->dropColumn('payment_type');
        });
    }
};
