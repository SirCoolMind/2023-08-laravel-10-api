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
            $table->string('artist_name')->null5able()->comment("Artist Name");
            $table->foreignId('kpop_era_id')->nullable()->comment("can ignore this first");
            $table->foreignId('kpop_era_version_id')->nullable()->comment("can ignore this first");
            $table->text('comment')->nullable()->comment("idk what to do yet");
            $table->integer('bought_price')->nullable();
            $table->string('bought_place')->nullable();
            $table->text('bought_comment')->nullable();
            $table->foreignId('user_id');
            $table->foreignId('project_id');
            $table->timestamps();

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
