<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const OLD_PREFIX = 'assets/tuvalet-kabin/EXCLUSIVE TYPE 1/';

    private const NEW_PREFIX = 'assets/tuvalet-kabin/exclusive-type-1/';

    public function up(): void
    {
        DB::update(
            'UPDATE `model_color_matches` SET `image_url` = REPLACE(`image_url`, ?, ?) WHERE `image_url` LIKE ?',
            [self::OLD_PREFIX, self::NEW_PREFIX, self::OLD_PREFIX.'%'],
        );
    }

    public function down(): void
    {
        DB::update(
            'UPDATE `model_color_matches` SET `image_url` = REPLACE(`image_url`, ?, ?) WHERE `image_url` LIKE ?',
            [self::NEW_PREFIX, self::OLD_PREFIX, self::NEW_PREFIX.'%'],
        );
    }
};
