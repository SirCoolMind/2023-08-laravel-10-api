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
        Schema::dropIfExists('money_balances');
        Schema::create('money_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('money_account_id');
            $table->timestamp('transaction_date', 0)->nullable();
            $table->bigInteger('balance');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Indexes
            // $table->index(['money_account_id', 'transaction_date']); //redundant
            $table->index('user_id');

            // Unique on each account, each day
            $table->unique(['transaction_date','money_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_balances');
        Schema::create('money_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('money_account_id');
            $table->bigInteger('balance');
            $table->timestamps();
        });
    }
};
