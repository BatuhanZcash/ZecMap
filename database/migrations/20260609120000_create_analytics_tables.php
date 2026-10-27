<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('stat_date')->unique();
            $table->unsignedBigInteger('page_views')->default(0);
            $table->unsignedInteger('sessions')->default(0);
            $table->unsignedBigInteger('business_views')->default(0);
            $table->timestamps();
        });

        Schema::create('analytics_dimension_stats', function (Blueprint $table) {
            $table->id();
            $table->date('stat_date');
            $table->string('dimension', 20);
            $table->string('dimension_key', 10);
            $table->unsignedBigInteger('hits')->default(0);
            $table->timestamps();

            $table->unique(['stat_date', 'dimension', 'dimension_key'], 'analytics_dimension_unique');
            $table->index(['dimension', 'stat_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_dimension_stats');
        Schema::dropIfExists('analytics_daily_stats');
    }
};
