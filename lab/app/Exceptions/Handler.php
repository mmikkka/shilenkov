<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     *
     * @var array
     *
     */
    protected $dontReport = [
        ValidationException::class,
        AuthenticationException::class,
        AuthorizationException::class,
        HttpResponseException::class,
        NotFoundHttpException::class,
        ModelNotFoundException::class,
        ThrottleRequestsException::class,
    ];

    /**
     * Рендер исключения в ответ на запрос.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response
     *@throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        // Ошибки валидации
        if ($e instanceof ValidationException) {
            return response()->json([
                'error' => 'Ошибка валидации',
                'message' => $e->errors(),
            ], 422);
        }

        // Ошибка аутентификации (например, если не передан токен)
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'error' => 'Не авторизован',
                'message' => 'Пожалуйста, войдите в систему.',
            ], 401);
        }

        // Ошибка авторизации (если прав недостаточно)
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'error' => 'Доступ запрещен',
                'message' => 'У вас нет прав для выполнения этого действия.',
            ], 403);
        }

        // Ошибка "Модель не найдена" (например, `User::findOrFail($id)`)
        if ($e instanceof ModelNotFoundException) {
            $model = class_basename($e->getModel());
            return response()->json([
                'error' => "$model не найден",
                'message' => 'Запрашиваемый ресурс отсутствует в базе данных.',
            ], 404);
        }

        // Ошибка "Страница не найдена"
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'Страница не найдена',
                'message' => 'Запрашиваемый URL не существует.',
            ], 404);
        }

        // Ошибка "Превышено количество запросов"
        if ($e instanceof ThrottleRequestsException) {
            return response()->json([
                'error' => 'Слишком много запросов',
                'message' => 'Попробуйте снова через некоторое время.',
            ], 429);
        }

        // Все остальные ошибки (например, ошибки сервера)
        if ($e instanceof HttpException) {
            return response()->json([
                'error' => 'Ошибка сервера',
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }

        // Любая другая неожиданная ошибка
        return response()->json([
            'error' => 'Внутренняя ошибка сервера',
            'message' => 'Произошла непредвиденная ошибка. Попробуйте позже.',
        ], 500);
    }
}
