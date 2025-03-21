<?php
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckExpiredTokens;
use Illuminate\Support\Facades\Route;
use App\Enums\TokenAbility;

// Группируем все маршруты с префиксом 'auth' и добавляем middleware для защищённых
Route::prefix('auth')->group(function () {
    // Маршруты, не требующие авторизации
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Обновление токена теперь требует refresh-токена с нужной возможностью
    Route::post('refresh', [AuthController::class, 'refreshToken'])->middleware([
        'auth:sanctum', // Сначала Sanctum проверяет токен
        'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value
    ]);

    // Маршруты, требующие авторизации
    Route::middleware(['auth:sanctum', CheckExpiredTokens::class, 'ability:' . TokenAbility::ACCESS_API->value])->group(function () {
        Route::get('me', [AuthController::class, 'infoUser']);
        Route::get('tokens', [AuthController::class, 'tokens']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout_all', [AuthController::class, 'logoutAll']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });
});
