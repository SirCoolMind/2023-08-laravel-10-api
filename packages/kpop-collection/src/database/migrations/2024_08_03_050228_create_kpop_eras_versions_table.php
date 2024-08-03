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
        Schema::create('kpop_eras_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpop_era_id');
            $table->string('name');
            $table->unsignedBigInteger('project_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpop_eras_versions');
    }
};
