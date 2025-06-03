<?php

namespace App\Jobs;

use App\Models\MessengerLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MessengerLogsExport;
use GuzzleHttp\Client;

class GenerateMessengerReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chatId;
    protected $days;

    public function __construct($chatId, $days = 1)
    {
        $this->chatId = $chatId;
        $this->days = $days;
    }

    public function handle()
    {
        // Генерируем отчет
        $fileName = 'messenger_logs_' . now()->format('Y-m-d_H-i') . '.xlsx';
        $filePath = storage_path('app/private/reports/' . $fileName);

        Excel::store(new MessengerLogsExport($this->days), 'reports/' . $fileName);

        // Отправляем файл в Telegram
        $this->sendFileToTelegram($filePath, $fileName);

        // Удаляем временный файл
        unlink($filePath);
    }

    protected function sendFileToTelegram($filePath, $fileName)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $url = "https://api.telegram.org/bot{$token}/sendDocument";

        $client = new Client([
            'verify' => false,
            'timeout' => 30,
        ]);

        try {
            $response = $client->post($url, [
                'multipart' => [
                    [
                        'name' => 'chat_id',
                        'contents' => $this->chatId
                    ],
                    [
                        'name' => 'document',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => $fileName
                    ],
                    [
                        'name' => 'caption',
                        'contents' => 'Отчет по логам мессенджеров за последние ' . $this->days . ' ' . trans_choice('день|дня|дней', $this->days)
                    ]
                ]
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            \Log::error('Ошибка отправки отчета в Telegram: ' . $e->getMessage());
            return false;
        }
    }
}
