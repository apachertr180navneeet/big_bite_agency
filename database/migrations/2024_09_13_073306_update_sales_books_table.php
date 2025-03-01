<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_books', function (Blueprint $table) {
            // Drop the total_tax column
            if (Schema::hasColumn('sales_books', 'total_tax')) {
                $table->dropColumn('total_tax');
            }

            // Add the new fields as strings
            $table->string('amount_before_tax')->nullable();
            $table->string('igst')->nullable();
            $table->string('cgst')->nullable();
            $table->string('sgst')->nullable();
            $table->string('recived_amount')->nullable();
            $table->string('balance_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_books', function (Blueprint $table) {
            // Add the total_tax column back
            $table->string('total_tax')->nullable();

            // Drop the new fields
            $table->dropColumn([
                'amount_before_tax',
                'igst',
                'cgst',
                'sgst',
                'recived_amount',
                'balance_amount',
            ]);
        });
    }
};
