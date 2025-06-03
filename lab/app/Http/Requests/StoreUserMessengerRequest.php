<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserMessengerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); //если пользователь авторизован
    }

    public function rules(): array
    {
        return [
            'messenger_id' => 'required|exists:messengers,id',
            'messenger_user_id' => 'required|string|max:255'
        ];
    }
}
