<?php

namespace App\Console\Commands;

use App\Models\ModelColorMatch;
use App\Models\Renk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Normalizer;

class ImportVsGovdeKapiModelColorMatchesCommand extends Command
{
    /**
     * Dosya adı: vs-govde-{govde-slug}--kapi-{kapi-slug}.png
     * govde -> color_id_1, kapi -> color_id_2
     * Sluglar, ModelColorMatchesFromOnBellekSeeder ile aynı slugifyText(name) ile üretilir.
     */
    protected $signature = 'model-color-matches:import-vs-govde-kapi
                            {--preset=exclusive-type-1 : Preset (php artisan list ile açıklamada özet); all-tuvalet-kabin-listed = 8 model toplu}
                            {--path= : public altı göreli klasör (doluysa preset yollarının yerine geçer; toplu preset ile kullanılamaz)}
                            {--model-id= : modeller.id (doluysa preset model id yerine geçer; toplu preset ile kullanılamaz)}
                            {--dry-run : Veritabanına yazmadan özet göster}';

    protected $description = 'vs-govde-*--kapi-* görsellerini model_color_matches tablosuna aktarır (tuvalet-kabin presetleri + toplu mod)';

    private const BULK_LISTED_PRESET_KEYS = [
        'orion-line',
        'proxline',
        'proxline-bini',
        'proxline-klasik',
        'steelline-bini',
        'steelline-delux',
        'steelline-klasik',
        'steelline-lux',
    ];

    private const BULK_PRESET_ID = 'all-tuvalet-kabin-listed';

    /** @var array<string, array{paths: list<string>, model_id: int}> */
    private const PRESETS = [
        'exclusive-type-1' => [
            'paths' => [
                'tuvalet-kabin/exclusive-type-1',
                'assets/tuvalet-kabin/exclusive-type-1',
                'tuvalet-kabin/EXCLUSIVE TYPE 1',
                'assets/tuvalet-kabin/EXCLUSIVE TYPE 1',
            ],
            'model_id' => 11,
        ],
        'multisilver' => [
            'paths' => [
                'tuvalet-kabin/multisilver',
                'assets/tuvalet-kabin/multisilver',
                'tuvalet-kabin/MULTISILVER',
                'assets/tuvalet-kabin/MULTISILVER',
            ],
            'model_id' => 3,
        ],
        'orion-line' => [
            'paths' => [
                'tuvalet-kabin/orion-line',
                'assets/tuvalet-kabin/orion-line',
                'tuvalet-kabin/ORION LINE',
                'assets/tuvalet-kabin/ORION LINE',
            ],
            'model_id' => 6,
        ],
        'proxline' => [
            'paths' => [
                'tuvalet-kabin/proxline',
                'assets/tuvalet-kabin/proxline',
                'tuvalet-kabin/PROXLINE',
                'assets/tuvalet-kabin/PROXLINE',
            ],
            'model_id' => 5,
        ],
        'proxline-bini' => [
            'paths' => [
                'tuvalet-kabin/proxline-bini',
                'assets/tuvalet-kabin/proxline-bini',
                'tuvalet-kabin/PROXLINE BİNİ',
                'assets/tuvalet-kabin/PROXLINE BİNİ',
            ],
            'model_id' => 8,
        ],
        'proxline-klasik' => [
            'paths' => [
                'tuvalet-kabin/proxline-klasik',
                'assets/tuvalet-kabin/proxline-klasik',
                'tuvalet-kabin/PROXLINE KLASİK',
                'assets/tuvalet-kabin/PROXLINE KLASİK',
            ],
            'model_id' => 2,
        ],
        'steelline-bini' => [
            'paths' => [
                'tuvalet-kabin/steelline-bini',
                'assets/tuvalet-kabin/steelline-bini',
                'tuvalet-kabin/STEELLINE BİNİ',
                'assets/tuvalet-kabin/STEELLINE BİNİ',
            ],
            'model_id' => 4,
        ],
        'steelline-delux' => [
            'paths' => [
                'tuvalet-kabin/steelline-delux',
                'assets/tuvalet-kabin/steelline-delux',
                'tuvalet-kabin/STEELLINE DELUX',
                'assets/tuvalet-kabin/STEELLINE DELUX',
            ],
            'model_id' => 10,
        ],
        'steelline-klasik' => [
            'paths' => [
                'tuvalet-kabin/steelline-klasik',
                'assets/tuvalet-kabin/steelline-klasik',
                'tuvalet-kabin/STEELLINE KLASİK',
                'assets/tuvalet-kabin/STEELLINE KLASİK',
            ],
            'model_id' => 7,
        ],
        'steelline-lux' => [
            'paths' => [
                'tuvalet-kabin/steelline-lux',
                'assets/tuvalet-kabin/steelline-lux',
                'tuvalet-kabin/STEELLINE LUX',
                'assets/tuvalet-kabin/STEELLINE LUX',
            ],
            'model_id' => 9,
        ],
    ];

    private function slugifyText(string $value): string
    {
        $s = (string) $value;
        $s = str_replace(['İ', 'I'], 'i', $s);
        $s = mb_strtolower($s, 'UTF-8');
        $s = Normalizer::normalize($s, Normalizer::FORM_D);
        $s = preg_replace('/[\x{0300}-\x{036f}]/u', '', $s);
        $s = str_replace(['ı', 'ş', 'ğ', 'ü', 'ö', 'ç'], ['i', 's', 'g', 'u', 'o', 'c'], $s);
        $s = preg_replace('/[^a-z0-9]+/u', '-', $s);
        $s = preg_replace('/^-+|-+$/u', '', $s);

        return $s ?? '';
    }

    /**
     * @param  list<string>  $relativeCandidates  public altı, / ile ayrılmış
     * @return array{0: string, 1: string}|null [relativePath, fullPath]
     */
    private function resolveDirectory(array $relativeCandidates): ?array
    {
        foreach ($relativeCandidates as $rel) {
            $rel = trim(str_replace('\\', '/', (string) $rel), '/');
            if ($rel === '') {
                continue;
            }
            $full = public_path($rel);
            if (File::isDirectory($full)) {
                return [$rel, $full];
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function explicitPathCandidates(string $relativePath): array
    {
        $rel = trim(str_replace('\\', '/', $relativePath), '/');
        $out = [$rel];
        if ($rel !== '' && ! str_starts_with($rel, 'assets/')) {
            $out[] = 'assets/'.$rel;
        }

        return $out;
    }

    /**
     * @return array<string, int>
     */
    private function buildSlugToRenkIdMap(): array
    {
        $slugToRenkId = [];
        foreach (Renk::query()->get(['id', 'name']) as $renk) {
            $slug = $this->slugifyText((string) $renk->name);
            if ($slug === '') {
                continue;
            }
            if (isset($slugToRenkId[$slug])) {
                $this->warn("Aynı slug birden fazla renk için: {$slug} (id: {$renk->id}, önceki id: {$slugToRenkId[$slug]})");
            }
            $slugToRenkId[$slug] = (int) $renk->id;
        }

        return $slugToRenkId;
    }

    /**
     * @param  array<string, int>  $slugToRenkId
     * @return array{imported: int, skipped: int}
     */
    private function importFromDirectory(
        string $relativePath,
        string $fullPath,
        int $modelId,
        bool $dryRun,
        array $slugToRenkId,
        string $presetLabel,
    ): array {
        $pattern = '/^vs-govde-(.+?)--kapi-(.+)\.(png|jpe?g)$/i';
        $imported = 0;
        $skipped = 0;

        foreach (File::files($fullPath) as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $basename = $file->getFilename();
            if (! preg_match($pattern, $basename, $m)) {
                $this->line("[{$presetLabel}] Atlandı (desen uymuyor): {$basename}");
                $skipped++;

                continue;
            }

            $govdeSlug = strtolower($m[1]);
            $kapiSlug = strtolower($m[2]);

            $colorId1 = $slugToRenkId[$govdeSlug] ?? null;
            $colorId2 = $slugToRenkId[$kapiSlug] ?? null;

            if ($colorId1 === null) {
                $this->warn("[{$presetLabel}] Gövde rengi eşleşmedi: {$govdeSlug} — {$basename}");
                $skipped++;

                continue;
            }
            if ($colorId2 === null) {
                $this->warn("[{$presetLabel}] Kapı rengi eşleşmedi: {$kapiSlug} — {$basename}");
                $skipped++;

                continue;
            }

            $imageUrl = $relativePath.'/'.$basename;

            if ($dryRun) {
                $this->line("[dry-run][{$presetLabel}] model={$modelId} color1={$colorId1} color2={$colorId2} → {$imageUrl}");
                $imported++;

                continue;
            }

            ModelColorMatch::updateOrCreate(
                [
                    'model_id' => $modelId,
                    'color_id_1' => $colorId1,
                    'color_id_2' => $colorId2,
                ],
                [
                    'image_url' => $imageUrl,
                    'status' => 'active',
                    'is_approved' => false,
                ],
            );
            $imported++;
        }

        return ['imported' => $imported, 'skipped' => $skipped];
    }

    public function handle(): int
    {
        $preset = strtolower(trim((string) $this->option('preset')));
        $pathOption = $this->option('path');
        $explicitPath = is_string($pathOption) ? trim(str_replace('\\', '/', $pathOption), '/') : '';
        $modelIdOpt = $this->option('model-id');
        $hasExplicitModelId = is_string($modelIdOpt) && $modelIdOpt !== '';
        $dryRun = (bool) $this->option('dry-run');

        $allowed = array_merge(array_keys(self::PRESETS), [self::BULK_PRESET_ID]);
        if (! in_array($preset, $allowed, true)) {
            $this->error('Geçersiz --preset. İzin verilenler: '.implode(', ', $allowed));

            return self::FAILURE;
        }

        if ($preset === self::BULK_PRESET_ID) {
            if ($explicitPath !== '') {
                $this->error('--path, '.self::BULK_PRESET_ID.' preset\'i ile birlikte kullanılamaz.');

                return self::FAILURE;
            }
            if ($hasExplicitModelId) {
                $this->error('--model-id, '.self::BULK_PRESET_ID.' preset\'i ile birlikte kullanılamaz.');

                return self::FAILURE;
            }

            return $this->runBulkListed($dryRun);
        }

        if (! isset(self::PRESETS[$preset])) {
            $this->error('Geçersiz --preset.');

            return self::FAILURE;
        }

        $presetConfig = self::PRESETS[$preset];

        if ($explicitPath !== '') {
            $resolved = $this->resolveDirectory($this->explicitPathCandidates($explicitPath));
            if ($resolved === null) {
                $this->error('Klasör bulunamadı. Denenen yollar: '.implode(', ', $this->explicitPathCandidates($explicitPath)));

                return self::FAILURE;
            }
        } else {
            $resolved = $this->resolveDirectory($presetConfig['paths']);
            if ($resolved === null) {
                $this->error('Klasör bulunamadı. Denenen yollar: '.implode(', ', $presetConfig['paths']));
                $this->line('İpucu: --path=assets/tuvalet-kabin/... ile tam yolu verin.');

                return self::FAILURE;
            }
        }

        [$relativePath, $fullPath] = $resolved;

        $modelId = $hasExplicitModelId
            ? (int) $modelIdOpt
            : $presetConfig['model_id'];

        $slugToRenkId = $this->buildSlugToRenkIdMap();
        $counts = $this->importFromDirectory($relativePath, $fullPath, $modelId, $dryRun, $slugToRenkId, $preset);

        $this->info($dryRun ? 'Dry-run tamamlandı.' : 'Aktarım tamamlandı.');
        $this->info("Klasör: {$relativePath}");
        $this->info('İşlenen (eşleşen): '.$counts['imported']);
        $this->info('Atlanan / desen dışı: '.$counts['skipped']);

        return self::SUCCESS;
    }

    private function runBulkListed(bool $dryRun): int
    {
        $slugToRenkId = $this->buildSlugToRenkIdMap();
        $grandImported = 0;
        $grandSkipped = 0;
        $missingFolders = [];

        foreach (self::BULK_LISTED_PRESET_KEYS as $key) {
            $config = self::PRESETS[$key];
            $resolved = $this->resolveDirectory($config['paths']);
            if ($resolved === null) {
                $missingFolders[] = $key.' ('.implode(' | ', $config['paths']).')';
                $this->warn("Klasör bulunamadı, atlandı: {$key}");

                continue;
            }
            [$relativePath, $fullPath] = $resolved;
            $modelId = $config['model_id'];
            $counts = $this->importFromDirectory($relativePath, $fullPath, $modelId, $dryRun, $slugToRenkId, $key);
            $grandImported += $counts['imported'];
            $grandSkipped += $counts['skipped'];
            $this->info("[{$key}] model={$modelId} klasör={$relativePath} → eşleşen: {$counts['imported']}, atlanan: {$counts['skipped']}");
        }

        $this->newLine();
        $this->info($dryRun ? 'Toplu dry-run tamamlandı.' : 'Toplu aktarım tamamlandı.');
        $this->info("Toplam eşleşen: {$grandImported}");
        $this->info("Toplam atlanan: {$grandSkipped}");
        if ($missingFolders !== []) {
            $this->warn('Bulunamayan klasörler ('.count($missingFolders).'):');
            foreach ($missingFolders as $line) {
                $this->line('  - '.$line);
            }
        }

        return self::SUCCESS;
    }
}
