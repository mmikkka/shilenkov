<?php

namespace App\Jobs;

use App\Exports\SystemReportExport;
use App\Models\ChangeLog;
use App\Models\LogRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected mixed $timeInterval;
    protected mixed $maxExecutionTime;
    protected mixed $timeoutBetweenRuns;
    protected mixed $repetitions;

    public function __construct()
    {
        $this->timeInterval = env('REPORT_TIME_INTERVAL_HOURS', 1);
        $this->maxExecutionTime = env('REPORT_MAX_EXECUTION_MINUTES', 1);
        $this->timeoutBetweenRuns = env('REPORT_TIMEOUT_BETWEEN_MINUTES', 1);
        $this->repetitions = env('REPORT_REPETITIONS', 1);
    }

    public function handle(): void
    {
        $this->generateAndSendReport();
    }

    protected function generateAndSendReport(): void
    {
        $dateFrom = Carbon::now()->subHours($this->timeInterval);

        $methodsRating = LogRequest::query()->select('action')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('MAX(called_at) as last_operation')
            ->where('called_at', '>=', $dateFrom)
            ->groupBy('action')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'method' => $item->action,
                    'count' => $item->count,
                    'last_operation' => $item->last_operation
                ];
            });

        $entitiesRating = ChangeLog::query()->select('entity_type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('MAX(created_at) as last_operation')
            ->where('created_at', '>=', $dateFrom)
            ->groupBy('entity_type')
            ->orderByDesc('count')
            ->get();

        $usersRating = User::with(['requests' => function ($query) use ($dateFrom) {
            $query->where('called_at', '>=', $dateFrom);
        }])
            ->with(['changes' => function ($query) use ($dateFrom) {
                $query->where('created_at', '>=', $dateFrom);
            }])
            ->get()
            ->map(function ($user) use ($dateFrom) {
                $lastRequest = $user->requests->max('called_at');
                $lastChange = $user->changes->max('created_at');

                return [
                    'user' => $user->username,
                    'requests_count' => $user->requests->count(),
                    'changes_count' => $user->changes->count(),
                    'authorizations_count' => $user->tokens()->count(),
                    'last_operation' => $lastRequest || $lastChange
                        ? max($lastRequest, $lastChange)
                        : null
                ];
            })
            ->sortByDesc(function ($user) {
                return $user['requests_count'] + $user['changes_count'];
            })
            ->values();

        // Генерируем Excel-файл
        $fileName = 'report_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        $storagePath = 'reports/' . $fileName;  // Относительный путь для store()
        $filePath = storage_path('app/private/' . $storagePath); // Полный путь для attach()

        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        Excel::store(
            new SystemReportExport($methodsRating, $entitiesRating, $usersRating, $dateFrom),
            $storagePath
        );

        $this->sendReportToAdmins($filePath);

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    protected function sendReportToAdmins(string $filePath): void
    {
        $admin = User::query()->findOrFail(4);
        Mail::send('emails.report', [], function ($message) use ($admin, $filePath) {
            $message->to($admin->email)
                ->subject('Системный отчет ' . now()->format('Y-m-d'))
                ->attach($filePath, [
                    'as' => 'system_report_' . now()->format('Ymd_His') . '.xlsx',
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
        });
        Log::info("Сообщение отправлено админу: {$admin->email}");
    }
}
