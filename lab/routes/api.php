<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChangeLogController;
use App\Http\Controllers\GitHookController;
use App\Http\Controllers\LogRequestController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TwoFaAuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckExpiredTokens;
use App\Http\Middleware\CheckPermission;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;


Route::prefix('hook')->group(function () {
    Route::get('git/{secretKey}', [GitHookController::class, 'handle']);
});

// Группируем все маршруты с префиксом 'auth' и добавляем middleware для защищённых
Route::prefix('auth')->group(function () {
    // Маршруты, не требующие авторизации
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Обновление токена теперь требует refresh-токена с нужной возможностью
    Route::post('refresh', [AuthController::class, 'refreshToken'])->middleware([
        'auth:sanctum', // Сначала Sanctum проверяет токен
        'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value,
    ]);

    // Маршруты, требующие авторизации
    Route::middleware(['auth:sanctum', CheckExpiredTokens::class, 'ability:' . TokenAbility::ACCESS_API->value])->group(function () {
        Route::prefix('2fa')->group(function () {
            Route::post('qr', [TwoFaAuthController::class, 'createQrCode']);
            Route::post('enable', [TwoFaAuthController::class, 'enable']);
            Route::post('disable', [TwoFaAuthController::class, 'disable']);
        });
        Route::get('me', [AuthController::class, 'infoUser']);
        Route::get('tokens', [AuthController::class, 'tokens']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout_all', [AuthController::class, 'logoutAll']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });
});

Route::prefix('ref')->group(function () {
    Route::middleware(['auth:sanctum', CheckExpiredTokens::class, 'ability:' . TokenAbility::ACCESS_API->value])->group(function () {
        // маршруты управления пользователями
        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'index'])
                ->middleware([CheckPermission::class . ':get-list-user']);
            Route::get('{user}/role', [UserController::class, 'showWithRoles'])
                ->middleware([CheckPermission::class . ':read-user']);
            Route::get('{id}/story', [UserController::class, 'story'])
                ->middleware(CheckPermission::class . ':get-story-user');
            Route::post('{user}/role/{role}', [UserController::class, 'attachRole'])
                ->middleware([CheckPermission::class . ':create-user']);
            Route::delete('{user}/role/{role}', [UserController::class, 'forceDetachRole'])
                ->middleware([CheckPermission::class . ':delete-user']);
            Route::delete('{user}/role/{role}/soft', [UserController::class, 'detachRole'])
                ->middleware([CheckPermission::class . ':delete-user']);
            Route::post('{user}/role/{role}/restore', [UserController::class, 'restoreRole'])
                ->middleware([CheckPermission::class . ':restore-user']);
        });

        Route::prefix('log/request')->group(function () {
            Route::get('/', [LogRequestController::class, 'index']);
            Route::get('{log}', [LogRequestController::class, 'show']);
            Route::delete('{log}', [LogRequestController::class, 'destroy']);
        });

        Route::prefix('changelog')->group(function () {
            Route::get('/', [ChangeLogController::class, 'index']);
            Route::post('{logId}/rollback', [ChangeLogController::class, 'rollback']);
        });

        Route::prefix('reports')->group(function () {
            Route::post('/generate', [ReportController::class, 'generateReport'])
                ->middleware([CheckPermission::class . ':read-role']);
        });

        // маршруты управления ролевой политикой (Роли)
        Route::prefix('policy/role')->group(function () {
            Route::get('/', [RoleController::class, 'index'])
                ->middleware([CheckPermission::class . ':get-list-role']);
            Route::get('{role}', [RoleController::class, 'show'])
                ->middleware([CheckPermission::class . ':read-role']);
            Route::get('{id}/story', [RoleController::class, 'story'])
                ->middleware(CheckPermission::class . ':get-story-role');
            Route::post('/', [RoleController::class, 'store'])
                ->middleware([CheckPermission::class . ':create-role']);
            Route::put('{role}', [RoleController::class, 'update'])
                ->middleware([CheckPermission::class . ':update-role']);
            Route::delete('{role}', [RoleController::class, 'forceDestroy'])
                ->middleware([CheckPermission::class . ':delete-role']);
            Route::delete('{role}/soft', [RoleController::class, 'destroy'])
                ->middleware([CheckPermission::class . ':delete-role']);
            Route::post('{role}/restore', [RoleController::class, 'restore'])
                ->middleware([CheckPermission::class . ':restore-role']);
        });

        // маршруты управления ролевой политикой (Разрешения)
        Route::prefix('policy/permission')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])
                ->middleware([CheckPermission::class . ':get-list-permission']);
            Route::get('{permission}', [PermissionController::class, 'show'])
                ->middleware([CheckPermission::class . ':read-permission']);
            Route::get('{id}/story', [PermissionController::class, 'story'])
                ->middleware(CheckPermission::class . ':get-story-permission');
            Route::post('/', [PermissionController::class, 'store'])
                ->middleware([CheckPermission::class . ':create-permission']);
            Route::put('{permission}', [PermissionController::class, 'update'])
                ->middleware([CheckPermission::class . ':update-permission']);
            Route::delete('{permission}', [PermissionController::class, 'forceDestroy'])
                ->middleware([CheckPermission::class . ':delete-permission']);
            Route::delete('{permission}/soft', [PermissionController::class, 'destroy'])
                ->middleware([CheckPermission::class . ':delete-permission']);
            Route::post('{permission}/restore', [PermissionController::class, 'restore'])
                ->middleware([CheckPermission::class . ':restore-permission']);
        });
    });
});
