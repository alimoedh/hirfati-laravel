<?php

namespace App\Console\Commands;

use App\Models\{Request, RequestArchive};
use App\Services\ActivityService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArchiveRequests extends Command
{
    protected $signature   = 'hirfati:archive-requests';
    protected $description = 'أرشفة الطلبات المكتملة الأقدم من 6 أشهر';

    public function handle(): int
    {
        $this->info('📦 بدء الأرشفة...');

        $cutoffDate = now()->subMonths(config('hirfati.archive.months_old', 6));
        $count = 0;

        DB::transaction(function () use ($cutoffDate, &$count) {
            Request::where('status', 'completed')
                ->where('created_at', '<', $cutoffDate)
                ->chunkById(100, function ($requests) use (&$count) {
                    foreach ($requests as $req) {
                        RequestArchive::create($req->toArray() + ['archived_at' => now()]);
                        $req->delete();
                        $count++;
                    }
                });
        });

        $this->info("✅ تم أرشفة {$count} طلب بنجاح");
        $this->line("📅 التاريخ: " . now()->format('Y-m-d H:i:s'));

        ActivityService::log(null, 'archive_requests', "تم أرشفة {$count} طلب قديم");

        return self::SUCCESS;
    }
}
