<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyMessengerRequest extends FormRequest
{

    public function authorize(): bool
    {
        return auth()->check(); //если пользователь авторизован
    }

    public function rules(): array
    {
        return [
            'messenger_id' => 'required|exists:messengers,id',
            'verification_code' => 'required|string|size:6'
        ];
    }
}
