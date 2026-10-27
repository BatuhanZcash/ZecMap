<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_reviews', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('community_id');
            $table->text('admin_note')->nullable()->after('status');
            $table->timestamp('reviewed_at')->nullable()->after('admin_note');
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
            $table->index('status');
        });

        DB::table('business_reviews')->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('business_reviews', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'admin_note', 'reviewed_at', 'reviewed_by']);
        });
    }
};
