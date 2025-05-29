<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LogRequest;
use Carbon\Carbon;

class CleanLogRequests extends Command
{
    protected $signature = 'logs:clean';
    protected $description = 'Удаление логов запросов, которые хранятся более 73 часов.';

    public function handle(): void
    {
        $cutoff = Carbon::now()->subHours(73);
        $deleted = LogRequest::query()->where('called_at', '<', $cutoff)->delete();

        $this->info("Удаление {$deleted} старых логов запросов.");
    }
}
