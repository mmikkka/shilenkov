<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TwoFaToggleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $user = $this->user();

        if (!Hash::check($this->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Неверный пароль.'],
            ]);
        }
    }
}
