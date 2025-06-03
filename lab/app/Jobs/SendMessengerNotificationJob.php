<?php

namespace App\Jobs;

use App\Models\MessengerLog;
use App\Models\UserMessenger;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMessengerNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $userMessenger;
    protected $message;
    protected $eventType;

    public function __construct(UserMessenger $userMessenger, string $message, string $eventType)
    {
        $this->userMessenger = $userMessenger;
        $this->message = $message;
        $this->eventType = $eventType;
    }

    public function handle(): void
    {
        $messenger = $this->userMessenger->messenger;
        $attemptNumber = $this->attempts();
        Log::info("Sending verification code to user {$messenger}");

        try {
            $success = false;

            // Отправка через API мессенджера
            if ($messenger->name === 'Telegram') {
                $token = env($messenger->token_env_name);
                $client = new Client([
                    'verify' => false,
                ]);


                $response = $client->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'form_params' => [
                        'chat_id' => $this->userMessenger->messenger_user_id,
                        'text' => $this->message,
                    ]
                ]);
            }

            // Логируем результат
            MessengerLog::create([
                'user_id' => $this->userMessenger->user_id,
                'messenger_id' => $this->userMessenger->messenger_id,
                'message' => $this->message,
                'status' => 1,
                'attempt_number' => $attemptNumber
            ]);

        } catch (\Exception $e) {
            MessengerLog::create([
                'user_id' => $this->userMessenger->user_id,
                'messenger_id' => $this->userMessenger->messenger_id,
                'message' => $this->message,
                'status' => false,
                'attempt_number' => $attemptNumber
            ]);

            $this->release(60); // Повторить через минуту
        }
    }
}
