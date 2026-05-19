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
        Schema::create('money_transaction_excel_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->date('transaction_date')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('description')->nullable();
            $table->string('type')->nullable(); // INCOME / EXPENSE / TRANSFER
            $table->string('money_account_name')->nullable();
            $table->string('money_category_name')->nullable();
            $table->string('money_subcategory_name')->nullable();
            $table->boolean('is_valid')->default(true);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('money_transaction_excel_batches');
    }
};
