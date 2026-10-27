<?php

namespace Database\Seeders;

use App\Models\ModelColorMatch;
use App\Models\Renk;
use App\Models\UrunModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Normalizer;

class ModelColorMatchesFromOnBellekSeeder extends Seeder
{
    private function slugifyText(string $value): string
    {
        // index.blade.php içindeki JS slugifyText fonksiyonunu birebir taklit eder.
        $s = (string) $value;
        $s = str_replace(["İ", "I"], "i", $s);
        $s = mb_strtolower($s, 'UTF-8');
        $s = Normalizer::normalize($s, Normalizer::FORM_D);
        $s = preg_replace('/[\x{0300}-\x{036f}]/u', '', $s);
        $s = str_replace(["ı", "ş", "ğ", "ü", "ö", "ç"], ["i", "s", "g", "u", "o", "c"], $s);
        $s = preg_replace('/[^a-z0-9]+/u', '-', $s);
        $s = preg_replace('/^-+|-+$/u', '', $s);
        return $s ?? '';
    }

    public function run(): void
    {
        $palette = [
            ['id' => 'cn-x01-ultra-white', 'label' => 'CN-X01 I ULTRA WHİTE', 'hex' => '#f3f3ec'],
            ['id' => 'cn-s01-beige', 'label' => 'CN-S01 I BEIGE', 'hex' => '#efebcf'],
            ['id' => 'cn-n01-magnolia', 'label' => 'CN-N01 I MAGNOLIA', 'hex' => '#ccc8b8'],
            ['id' => 'cn-g01-office-gray', 'label' => 'CN-G01 I OFFICE GRAY', 'hex' => '#d5d3d1'],
            ['id' => 'cn-g03-dark-grey', 'label' => 'CN-G03 I DARK GREY', 'hex' => '#878a87'],
            ['id' => 'cn-g02-anthracite', 'label' => 'CN-G02 I ANTHRACITE', 'hex' => '#434544'],
            ['id' => 'cn-x03-mystik-new', 'label' => 'CN-X03 I MYSTIK NEW', 'hex' => '#ac969a'],
            ['id' => 'cn-a01-azul-blue', 'label' => 'CN-A01 I AZUL BLUE', 'hex' => '#405080'],
            ['id' => 'cn-a02-orange', 'label' => 'CN-A02 I ORANGE', 'hex' => '#d27648'],
            ['id' => 'cn-x01-chrome-yellow', 'label' => 'CN-X01 CHROME YELLOW', 'hex' => '#ebc478'],
        ];

        // renk -> id eşlemesi
        $renkCodeToId = Renk::query()
            ->whereNotNull('code')
            ->pluck('id', 'code')
            ->mapWithKeys(fn ($id, $code) => [strtolower((string) $code) => $id])
            ->toArray();

        $idSlugToColorId = [];
        $labelSlugToColorId = [];

        foreach ($palette as $p) {
            $hex = strtolower($p['hex']);
            if (!isset($renkCodeToId[$hex])) {
                // palette DB'de yoksa ilerlemeyi engellemeyelim.
                continue;
            }

            $colorId = (int) $renkCodeToId[$hex];
            $idSlugToColorId[$this->slugifyText($p['id'])] = $colorId;
            $labelSlugToColorId[$this->slugifyText($p['label'])] = $colorId;
        }

        // modelSlug -> modelId eşlemesi (kabinler/dolaplar klasörüne göre tip kırpılır)
        $kabinModels = UrunModel::query()->whereIn('type', ['kabin', 'standart'])->get(['id', 'name', 'type']);
        $dolapModels = UrunModel::query()->whereIn('type', ['dolap', 'premium'])->get(['id', 'name', 'type']);

        $kabinModelSlugToId = [];
        foreach ($kabinModels as $m) {
            $slug = $this->slugifyText((string) $m->name);
            if ($slug !== '' && !isset($kabinModelSlugToId[$slug])) {
                $kabinModelSlugToId[$slug] = (int) $m->id;
            }
        }

        $dolapModelSlugToId = [];
        foreach ($dolapModels as $m) {
            $slug = $this->slugifyText((string) $m->name);
            if ($slug !== '' && !isset($dolapModelSlugToId[$slug])) {
                $dolapModelSlugToId[$slug] = (int) $m->id;
            }
        }

        $root = public_path('on-bellek');
        if (!File::exists($root)) {
            $this->command?->info("'public/on-bellek' klasörü bulunamadı. Seeder durduruldu.");
            return;
        }

        $files = File::allFiles($root);

        $processed = 0;
        $upserted = 0;
        $skipped = 0;

        foreach ($files as $file) {
            if (!$file->isFile()) continue;

            $ext = strtolower($file->getExtension());
            if ($ext !== 'jpg' && $ext !== 'jpeg' && $ext !== 'png') continue;

            $relative = str_replace('\\', '/', $file->getRelativePathname());
            // relative: kabinler/{modelSlug}/{bodySlug}/{doorSlug}.jpg
            $relativeParts = explode('/', $relative);
            if (count($relativeParts) < 4) {
                $skipped++;
                continue;
            }

            $category = $relativeParts[0]; // kabinler | dolaplar
            $productSlug = $relativeParts[1];
            $bodySlug = $relativeParts[2];
            $doorFile = $relativeParts[3];

            $doorSlug = preg_replace('/\\.(jpe?g|png)$/i', '', $doorFile);
            if ($doorSlug === '' || $bodySlug === '' || $productSlug === '') {
                $skipped++;
                continue;
            }

            if ($category === 'kabinler') {
                $modelId = $kabinModelSlugToId[$productSlug] ?? null;
                $colorId1 = $idSlugToColorId[$bodySlug] ?? null;
                $colorId2 = $idSlugToColorId[$doorSlug] ?? null;
            } elseif ($category === 'dolaplar') {
                $modelId = $dolapModelSlugToId[$productSlug] ?? null;
                $colorId1 = $labelSlugToColorId[$bodySlug] ?? null;
                $colorId2 = $labelSlugToColorId[$doorSlug] ?? null;
            } else {
                $skipped++;
                continue;
            }

            if (!$modelId || !$colorId1 || !$colorId2) {
                $skipped++;
                continue;
            }

            $imageUrl = 'on-bellek/' . $category . '/' . $productSlug . '/' . $bodySlug . '/' . $doorSlug . '.' . $ext;

            ModelColorMatch::updateOrCreate(
                [
                    'model_id' => $modelId,
                    'color_id_1' => $colorId1,
                    'color_id_2' => $colorId2,
                ],
                [
                    'image_url' => $imageUrl,
                    'status' => 'active',
                ],
            );

            $processed++;
            $upserted++;
        }

        $this->command?->info('ModelColorMatchesFromOnBellekSeeder tamamlandı.');
        $this->command?->info('İşlenen: ' . $processed);
        $this->command?->info('Atlanan: ' . $skipped);
    }
}

