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
        Schema::create('money_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_account_id');
            $table->unsignedBigInteger('target_account_id');
            $table->bigInteger('balance');
            $table->timestamp('transaction_date', 0)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Indexes
            $table->index(['transaction_date', 'user_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_transfers');
    }
};
