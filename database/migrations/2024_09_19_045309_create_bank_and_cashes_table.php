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
        Schema::create('bank_and_cashes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('serial_no')->unique();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('amount');
            $table->enum('payment_take',['deposit','withdraw'])->default('deposit');
            $table->enum('payment_type',['cash','bank'])->default('cash');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_and_cashes');
    }
};
