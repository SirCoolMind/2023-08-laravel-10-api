<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('money_accounts', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
        Schema::table('money_categories', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
        Schema::table('money_subcategories', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Set all NULL description values to an empty string (or any default)
        DB::table('money_accounts')
            ->whereNull('description')
            ->update(['description' => '']);
        DB::table('money_categories')
            ->whereNull('description')
            ->update(['description' => '']);
        DB::table('money_subcategories')
            ->whereNull('description')
            ->update(['description' => '']);

        Schema::table('money_accounts', function (Blueprint $table) {
            $table->text('description')->nullable(false)->change();
        });
        Schema::table('money_categories', function (Blueprint $table) {
            $table->text('description')->nullable(false)->change();
        });
        Schema::table('money_subcategories', function (Blueprint $table) {
            $table->text('description')->nullable(false)->change();
        });
    }
};
