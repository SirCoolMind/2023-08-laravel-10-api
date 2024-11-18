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
        Schema::table('money_transactions', function (Blueprint $table) {
            $table->foreignId('money_subcategory_id')->after('description')->nullable();
            $table->foreignId('money_category_id')->after('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('money_transactions', function (Blueprint $table) {
            $table->dropColumn('money_category_id');
            $table->dropColumn('money_subcategory_id');
        });
    }
};
