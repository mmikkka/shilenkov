<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserMessengerRequest;
use App\Http\Requests\VerifyMessengerRequest;
use App\Jobs\SendVerificationCodeJob;
use App\Models\Messenger;
use App\Models\User;
use App\Models\UserMessenger;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MessengerController extends Controller
{
    public function index(): JsonResponse
    {
        $messengers = Messenger::query()->where('environment', app()->environment())->get();
        return response()->json($messengers);
    }

    public function store(StoreUserMessengerRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Необходима авторизация'], 401);
        }

        // Генерируем 6-значный код подтверждения
        $verificationCode = Str::padLeft(random_int(0, 999999), 6, '0');
        $expiresAt = Carbon::now()->addMinutes(15);

        $userMessenger = UserMessenger::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'messenger_id' => $request->messenger_id
            ],
            [
                'messenger_user_id' => $request->messenger_user_id,
                'verification_code' => $verificationCode,
                'verification_code_sent_at' => Carbon::now(),
                'verification_code_expires_at' => $expiresAt,
                'is_confirmed' => false,
                'allow_notifications' => true
            ]
        );

        $userMessenger->load('messenger');

        Log::info("Created or updated userMessenger {$userMessenger}");

        // Отправляем код через очередь
        SendVerificationCodeJob::dispatch($userMessenger, $verificationCode);

        return response()->json([
            'message' => 'Код подтверждения отправлен. Проверьте мессенджер.',
            'expires_at' => $expiresAt->toDateTimeString()
        ], 201);
    }

    public function verify(VerifyMessengerRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Необходима авторизация'], 401);
        }

        try {
            $messenger = Messenger::query()->findOrFail($request->messenger_id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Мессенджер не найден'], 404);
        }

        $userMessenger = $user->userMessengers()
            ->where('messenger_id', $messenger->id)
            ->where('verification_code', $request->verification_code)
            ->where('verification_code_expires_at', '>', Carbon::now())
            ->first();

        if (!$userMessenger) {
            return response()->json(['message' => 'Неверный или просроченный код подтверждения'], 400);
        }

        $userMessenger->update([
            'is_confirmed' => true,
            'confirmed_at' => Carbon::now(),
            'verification_code' => null,
            'verification_code_expires_at' => null
        ]);

        return response()->json(['message' => 'Мессенджер успешно подтвержден']);
    }

    public function resendCode($messengerId): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $userMessenger = $user->userMessengers()
            ->where('messenger_id', $messengerId)
            ->where('is_confirmed', false)
            ->firstOrFail();

        // Генерируем новый код
        $verificationCode = Str::padLeft(random_int(0, 999999), 6, '0');
        $expiresAt = Carbon::now()->addMinutes(15);

        $userMessenger->update([
            'verification_code' => $verificationCode,
            'verification_code_sent_at' => Carbon::now(),
            'verification_code_expires_at' => $expiresAt
        ]);

        SendVerificationCodeJob::dispatch($userMessenger, $verificationCode);

        return response()->json([
            'message' => 'Новый код подтверждения отправлен',
            'expires_at' => $expiresAt->toDateTimeString()
        ]);
    }
}
