<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
{
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'error' => 'Ошибка валидации:(',
                'message' => $validator->errors(),
            ], 422)
        );
    }

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string'
            ],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
            'new_password_confirmation'  => [
                'required',
                'string',
                'same:new_password',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'new_password.required' => 'Пароль обязателен.',
            'new_password.string' => 'Пароль должен быть строкой.',
            'new_password.min' => 'Пароль должен содержать минимум 8 символов.',
            'new_password.regex' => 'Пароль должен содержать хотя бы одну заглавную букву, одну строчную букву, одну цифру и один спецсимвол.',

            'new_password_confirmation.required' => 'Подтверждение пароля обязательно.',
            'new_password_confirmation.string' => 'Подтверждение пароля должно быть строкой.',
            'new_password_confirmation.same' => 'Пароль и подтверждение пароля должны совпадать.',
        ];
    }
}
