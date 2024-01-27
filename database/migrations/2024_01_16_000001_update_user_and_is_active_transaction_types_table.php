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
        Schema::table('transaction_types', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->after('id');
            $table->boolean('is_active')->default(true)->after('description');
            $table->dropColumn('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_types', function (Blueprint $table) {
            $table->boolean('is_completed')->default(false);
            $table->dropColumn('is_active');
            $table->dropColumn('user_id');
        });
    }
};
