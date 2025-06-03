<?php

namespace App\Jobs;

use App\Models\UserMessenger;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendVerificationCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly UserMessenger $userMessenger,
        private readonly string        $verificationCode
    )
    {
    }

    public function handle(): void
    {
        $messenger = $this->userMessenger->messenger;
        Log::info("Sending verification code to user {$messenger}");

        if ($messenger->name === 'Telegram') {
            $token = env($messenger->token_env_name);
            $message = "Ваш код подтверждения: {$this->verificationCode}\nКод действителен 15 минут.";

            $client = new Client([
                'verify' => false,
            ]);

            $client->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'form_params' => [
                    'chat_id' => $this->userMessenger->messenger_user_id,
                    'text' => $message,
                ]
            ]);
        }
    }
}
