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
        Schema::table('variations', function (Blueprint $table) {
            // Adding foreign keys to company_id columns
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variations', function (Blueprint $table) {
             // Dropping the foreign keys
             $table->dropForeign(['company_id']);
        });
    }
};
