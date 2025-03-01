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
        Schema::create('companies', function (Blueprint $table) {
            // Defining the id column
            $table->id();

            // Defining the columns for the company
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('address')->nullable();
            $table->string('gstin')->nullable();
            $table->string('city')->nullable();
            $table->enum('type', ['type1', 'type2'])->default('type1');
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Defining timestamps and soft delete columns
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
