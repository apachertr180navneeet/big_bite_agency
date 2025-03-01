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
        Schema::table('bank_and_cashes', function (Blueprint $table) {
            // Remove the enum fields
            $table->dropColumn('payment_take');
            $table->dropColumn('payment_type');

            // Add the new fields
            $table->string('deposite_in');
            $table->string('withdraw_in');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_and_cashes', function (Blueprint $table) {
            // Re-add the enum fields
            $table->enum('payment_take', ['deposit', 'withdraw'])->default('deposit');
            $table->enum('payment_type', ['cash', 'bank'])->default('cash');

            // Remove the new fields
            $table->dropColumn('deposite_in');
            $table->dropColumn('withdraw_in');
        });
    }
};
