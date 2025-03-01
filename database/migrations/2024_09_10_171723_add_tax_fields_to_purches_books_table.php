<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxFieldsToPurchesBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purches_books', function (Blueprint $table) {
            $table->string('igst')->nullable()->after('total_tax');
            $table->string('sgst')->nullable()->after('IGST');
            $table->string('cgst')->nullable()->after('SGST');
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
            $table->dropColumn(['igst', 'sgst', 'cgst']);
        });
    }
}
