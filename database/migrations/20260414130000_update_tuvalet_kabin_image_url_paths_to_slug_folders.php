<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * PROXLINE alt klasörleri, düz PROXLINE/ önekinden önce güncellenmeli.
     *
     * @var list<array{old: string, new: string}>
     */
    private const TO_SLUG = [
        ['old' => 'assets/tuvalet-kabin/PROXLINE BİNİ/', 'new' => 'assets/tuvalet-kabin/proxline-bini/'],
        ['old' => 'assets/tuvalet-kabin/PROXLINE KLASİK/', 'new' => 'assets/tuvalet-kabin/proxline-klasik/'],
        ['old' => 'assets/tuvalet-kabin/PROXLINE/', 'new' => 'assets/tuvalet-kabin/proxline/'],
        ['old' => 'assets/tuvalet-kabin/STEELLINE BİNİ/', 'new' => 'assets/tuvalet-kabin/steelline-bini/'],
        ['old' => 'assets/tuvalet-kabin/STEELLINE DELUX/', 'new' => 'assets/tuvalet-kabin/steelline-delux/'],
        ['old' => 'assets/tuvalet-kabin/STEELLINE KLASİK/', 'new' => 'assets/tuvalet-kabin/steelline-klasik/'],
        ['old' => 'assets/tuvalet-kabin/STEELLINE LUX/', 'new' => 'assets/tuvalet-kabin/steelline-lux/'],
        ['old' => 'assets/tuvalet-kabin/ORION LINE/', 'new' => 'assets/tuvalet-kabin/orion-line/'],
        ['old' => 'assets/tuvalet-kabin/MULTISILVER/', 'new' => 'assets/tuvalet-kabin/multisilver/'],
    ];

    public function up(): void
    {
        foreach (self::TO_SLUG as $pair) {
            DB::update(
                'UPDATE `model_color_matches` SET `image_url` = REPLACE(`image_url`, ?, ?) WHERE `image_url` LIKE ?',
                [$pair['old'], $pair['new'], $pair['old'].'%'],
            );
        }
    }

    public function down(): void
    {
        foreach (array_reverse(self::TO_SLUG) as $pair) {
            DB::update(
                'UPDATE `model_color_matches` SET `image_url` = REPLACE(`image_url`, ?, ?) WHERE `image_url` LIKE ?',
                [$pair['new'], $pair['old'], $pair['new'].'%'],
            );
        }
    }
};
