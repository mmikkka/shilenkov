<?php

namespace App\Http\Controllers;

use App\Http\Requests\TwoFaToggleRequest;
use App\Models\User;
use BaconQrCode\Renderer\Image\EpsImageBackEnd;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA as Google2FAQRCode;
use PragmaRX\Google2FA\Google2FA;

class TwoFaAuthController extends Controller
{
    public function __construct(private readonly Google2FA $google2FA)
    {
    }

    /**
     * создает qr-код для регистрации приложения в google authenticator
     */
    public function createQrCode(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->google2fa_enabled) {
            return response()->json(['message' => '2FA отключена'], 400);
        }

        $qrCode = (new Google2FAQRCode(imageBackEnd: new SvgImageBackEnd()))->getQRCodeInline(
            config('app.name'), $user->username, $user->google2fa_secret
        );

        return response()->json(['qrcode' => $qrCode]);
    }

    /**
     * Включает 2FA
     */
    public function enable(TwoFaToggleRequest $request): JsonResponse
    {
        $request->authenticate();
        $user = $request->user();

        if ($user->google2fa_enabled) {
            return response()->json(["message" => "2FA уже включена"], 400);
        }

        $user->google2fa_enabled = true;
        $user->google2fa_secret = $this->google2FA->generateSecretKey();
        $user->save();

        return response()->json(['message' => '2FA авторизация включена']);
    }

    /**
     * Отключает 2FA
     */
    public function disable(TwoFaToggleRequest $request): JsonResponse
    {
        $request->authenticate();
        $user = $request->user();

        if (!$user->google2fa_enabled) {
            return response()->json(["message" => "2FA уже отключена"], 400);
        }

        $user->google2fa_enabled = false;
        $user->google2fa_secret = null;
        $user->save();

        return response()->json(['message' => '2FA авторизация отключена']);
    }
}
