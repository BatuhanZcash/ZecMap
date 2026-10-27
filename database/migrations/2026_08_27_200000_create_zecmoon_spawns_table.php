<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zecmoon_spawns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('window_index');
            $table->unsignedTinyInteger('slot')->default(0);
            $table->unsignedInteger('seq_no')->unique();
            $table->unsignedInteger('capture_no')->nullable()->unique();
            $table->double('lat');
            $table->double('lng');
            $table->foreignId('business_id')->nullable()->constrained('businesses')->nullOnDelete();
            $table->timestamp('spawn_at');
            $table->timestamp('expires_at');
            $table->foreignId('captured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('captured_at')->nullable();
            $table->string('captured_ip', 45)->nullable();
            $table->timestamps();

            $table->unique(['window_index', 'slot']);
            $table->index(['captured_by', 'captured_at']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zecmoon_spawns');
    }
};
