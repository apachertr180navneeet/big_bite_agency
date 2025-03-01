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
        Schema::table('items', function (Blueprint $table) {
            // Ensure the columns are unsigned if the referenced id columns are unsigned
            $table->unsignedBigInteger('company_id')->nullable()->change();
            $table->unsignedBigInteger('variation_id')->nullable()->change();
            $table->unsignedBigInteger('tax_id')->nullable()->change();

            // Adding foreign keys to company_id, variation_id, and tax_id columns
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Dropping the foreign keys
            $table->dropForeign(['company_id']);
            $table->dropForeign(['variation_id']);
            $table->dropForeign(['tax_id']);
        });
    }
};
