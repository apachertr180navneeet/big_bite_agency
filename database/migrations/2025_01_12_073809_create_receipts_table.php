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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('receipt')->unique();
            $table->BigInteger('bill_id');
            $table->string('amount');
            $table->string('discount');
            $table->enum('full_payment',['yes','no'])->default('no');
            $table->enum('manager_status',['active','inactive'])->default('active');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
