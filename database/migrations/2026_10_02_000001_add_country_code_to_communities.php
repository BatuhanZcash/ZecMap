<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Links a community to a region so its page can auto list the businesses in
 * that country. country_code is nullable (region communities like Europe /
 * Global may have none). Backfills the known communities by slug.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('communities', 'country_code')) {
            Schema::table('communities', function (Blueprint $table) {
                $table->string('country_code', 2)->nullable()->after('flag_emoji')->index();
            });
        }

        $map = [
            'brazil' => 'BR', 'canada' => 'CA', 'germany' => 'DE', 'japan' => 'JP',
            'turkey' => 'TR', 'united-kingdom' => 'GB', 'united-states' => 'US',
        ];
        foreach ($map as $slug => $cc) {
            DB::table('communities')->where('slug', $slug)->whereNull('country_code')->update(['country_code' => $cc]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('communities', 'country_code')) {
            Schema::table('communities', function (Blueprint $table) {
                $table->dropColumn('country_code');
            });
        }
    }
};
