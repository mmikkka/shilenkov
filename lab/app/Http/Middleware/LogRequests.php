<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    /**
     * Обрабатывает приходящий запрос
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        // Пропускаем логирование эндпоинтов самих логов
        if (Str::startsWith($request->path(), 'api/ref/log/request')) {
            return $response;
        }

        $controller = $request->route()?->getControllerClass();
        $action = $request->route()?->getActionMethod();

        LogRequest::query()->create([
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'controller' => $controller,
            'action' => $action,
            'request_body' => $this->sanitizeData($request->all()),
            'request_headers' => $this->sanitizeHeaders($request->headers->all()),
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status_code' => $response->getStatusCode(),
            'response_body' => $this->sanitizeData(json_decode($response->getContent(), true) ?: []),
            'response_headers' => $this->sanitizeHeaders($response->headers->all()),
            'called_at' => now(),
        ]);

        return $response;
    }

    private function sanitizeData(array $data): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'access_token', 'refresh_token'];

        array_walk_recursive($data, function (&$value, $key) use ($sensitiveKeys) {
            if (in_array($key, $sensitiveKeys, true)) {
                $value = str_repeat('*', strlen($value));
            }
        });

        return $data;
    }

    private function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = ['authorization', 'cookie', 'set-cookie'];

        foreach ($headers as $key => &$values) {
            if (in_array(strtolower($key), $sensitiveHeaders, true)) {
                $values = array_map(fn($v) => str_repeat('*', strlen($v)), $values);
            }
        }

        return $headers;
    }
}
