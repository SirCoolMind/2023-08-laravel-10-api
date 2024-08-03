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
        Schema::create('kpop_items', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->text('comment');
            $table->integer('bought_price');
            $table->string('bought_place');
            $table->text('bought_comment');
            $table->foreignId('user_id');
            $table->foreignId('project_id');
            $table->timestamps();

            $table->index('type');
            $table->index('bought_place');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpop_items');
    }
};
