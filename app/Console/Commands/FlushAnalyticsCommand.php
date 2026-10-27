<?php

namespace App\Console\Commands;

use App\Services\AnalyticsDashboard;
use App\Services\AnalyticsFlusher;
use Illuminate\Console\Command;

class FlushAnalyticsCommand extends Command
{
    protected $signature = 'analytics:flush {--date= : Belirli bir günü (Y-m-d) diske yaz}';

    protected $description = 'Eski cache analitik sayaçlarını veritabanına aktarır';

    public function handle(AnalyticsFlusher $flusher): int
    {
        $date = $this->option('date');
        $flushed = $flusher->flush(is_string($date) && $date !== '' ? $date : null);

        AnalyticsDashboard::forgetDashboardCache();

        $this->info($flushed > 0
            ? "{$flushed} günün analitik verisi kaydedildi."
            : 'Aktarılacak bekleyen analitik veri yok.');

        return self::SUCCESS;
    }
}
