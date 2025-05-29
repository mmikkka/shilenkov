<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GitHookController extends Controller
{
    public function handle(Request $request, string $secretKey): JsonResponse
    {
        $envSecret = env('GIT_HOOK_SECRET');

        if (!Str::is($envSecret, $secretKey)) {
            Log::warning('Invalid secret key attempt', [
                'ip' => $request->ip(),
                'date' => now(),
            ]);
            return response()->json(['message' => "Неверный ключ"], 403);
        }

        if (Cache::get('git_hook_running', false)) {
            return response()->json(['message' => "Обновление уже запущено"], 409);
        }

        try {
            Cache::put('git_hook_running', true, now()->addMinutes(5));

            Log::info('Git hook triggered', [
                'ip' => $request->ip(),
                'date' => now(),
            ]);

            $this->runGitCommands();

            return response()->json(["message" => "Проект успешно обновлен"]);
        } finally {
            // Снимаем флаг выполнения
            Cache::forget('git_hook_running');
        }
    }

    protected function runGitCommands(): void
    {
        $reset = Process::run('git reset');
        Log::info('Git reset output: ' . $reset->output());

        $checkout = Process::run('git checkout main');
        Log::info('Git checkout output: ' . $checkout->output());

        $pull = Process::run('git pull origin main');
        Log::info('Git pull output: ' . $pull->output());
    }
}
