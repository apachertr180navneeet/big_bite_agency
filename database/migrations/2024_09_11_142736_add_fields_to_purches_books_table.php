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
            $table->string('amount_before_tax')->nullable();
            $table->string('given_amount')->nullable();
            $table->string('remaining_blance')->nullable();
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
            $table->dropColumn('amount_before_tax');
            $table->dropColumn('given_amount');
            $table->dropColumn('remaining_blance');
        });
    }
};
