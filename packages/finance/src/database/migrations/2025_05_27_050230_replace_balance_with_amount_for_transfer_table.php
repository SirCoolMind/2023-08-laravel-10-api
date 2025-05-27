<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('money_transfers', function (Blueprint $table) {
            $table->string('description')->nullable()->after('transaction_date');
            $table->bigInteger('amount')->after('target_account_id');
            $table->dropColumn('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('money_transfers', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->bigInteger('balance')->after('target_account_id');
            $table->dropColumn('amount');
        });
    }
};
