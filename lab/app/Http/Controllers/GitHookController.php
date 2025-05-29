<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GitHookController extends Controller
{
    /**
     * Обработка запроса
     */
    public function handle(Request $request, string $secretKey): JsonResponse
    {
        $envSecret = env('GIT_HOOK_SECRET');

        if (!Str::is($envSecret, $secretKey)) {
            Log::warning('Invalid secret key attempt', [
                'ip' => $request->ip(),
                'date' => now(),
            ]);
            return response()->json(['message' => 'Неверный ключ'], 403);
        }

        # Проверка запущена обработка или нет
        if (Cache::get('git_hook_running', false)) {
            return response()->json(['message' => 'Обновление уже запущено'], 409);
        }

        try {
            Cache::put('git_hook_running', true, now()->addMinutes(5));

            Log::info('Git hook triggered', [
                'ip' => $request->ip(),
                'date' => now(),
            ]);

            $this->runGitCommands();

            return response()->json(['message' => 'Проект успешно обновлен']);
        } catch (\Exception $e) {
            Log::error('Git hook failed', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'date' => now(),
            ]);
            return response()->json(['message' => 'Ошибка при обновлении проекта', 'error' => $e->getMessage()], 500);
        } finally {
            Cache::forget('git_hook_running');
        }
    }

    /**
     * Запуск выполнения команд git
     */
    protected function runGitCommands(): void
    {
        $repoPath = base_path();

        $commands = [
            ['git', 'reset', "--hard"], # Удаляет все не закомиченные изменения
            ['git', 'checkout', 'master'], # Переходит на ветку master
            ['git', 'pull'], # Подтягивает все изменения из удаленного репозитория и сохраняет их локально
        ];

        foreach ($commands as $command) {
            try {
                $process = Process::path($repoPath)
                    ->timeout(60)
                    ->run($command);

                Log::info('Git command executed', [
                    'command' => implode(' ', $command),
                    'output' => $process->output(),
                    'error_output' => $process->errorOutput(),
                ]);

                if (!$process->successful()) {
                    throw new ProcessFailedException($process);
                }
            } catch (ProcessFailedException $e) {
                Log::error('Git command failed', [
                    'command' => implode(' ', $command),
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }
    }
}
