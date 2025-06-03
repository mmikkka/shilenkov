<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateMessengerReportJob;
use App\Jobs\SendMessengerNotificationJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessengerReportController extends Controller
{
    public function handleCommand(Request $request): JsonResponse
    {
        $user = User::query()->findOrFail(Auth::id());
        foreach ($user->userMessengers()->where('is_confirmed', true)->where('allow_notifications', true)->get() as $userMessenger) {
            $userMessenger->load('messenger');
            $this->processLogsCommand($userMessenger, "Формирую отчет за 30 дней", 30, "update");
        }

        return response()->json(['status' => 'ok']);
    }

    protected function processLogsCommand($userMessenger, $message, $days, $eventType): void
    {
        SendMessengerNotificationJob::dispatch($userMessenger, $message, $eventType);
        GenerateMessengerReportJob::dispatch($userMessenger->messenger_user_id, $days);
    }
}
