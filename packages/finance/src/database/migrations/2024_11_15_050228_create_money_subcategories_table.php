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
        Schema::create('money_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\HafizRuslan\Finance\app\Models\MoneyCategory::class);
            $table->string('name');
            $table->text('description');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_subcategories');
    }
};
