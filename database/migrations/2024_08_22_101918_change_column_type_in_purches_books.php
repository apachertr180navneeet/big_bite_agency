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
        Schema::table('purches_books', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['company_id']);

            // Modify columns
            $table->string('total_tax')->change();
            $table->string('other_expense')->change();
            $table->string('discount')->change();
            $table->string('round_off')->change();
            $table->string('grand_total')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purches_books', function (Blueprint $table) {
            // Revert columns
            $table->decimal('total_tax', 8, 2)->change();
            $table->decimal('other_expense', 8, 2)->change();
            $table->decimal('discount', 8, 2)->change();
            $table->decimal('round_off', 8, 2)->change();
            $table->decimal('grand_total', 8, 2)->change();

            // Re-add foreign key constraints
            $table->foreign('vendor_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }
};
