<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    // Переопределение метода failedValidation для кастомной обработки ошибок
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
            'username' => [
                'required',
                'string',
                'min:7',
                'regex:/^[A-Z][a-zA-z]+$/',
                'unique:users,username',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],

            'password_confirmation' => [
                'required',
                'string',
                'same:password',
            ],

            'birthday' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:' . now()->subYears(14)->format('Y-m-d'),
            ]
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
            'username.required' => 'Имя пользователя обязательно.',
            'username.string' => 'Имя пользователя должно быть строкой.',
            'username.min' => 'Имя пользователя должно содержать минимум 7 символов.',
            'username.regex' => 'Имя пользователя должно начинаться с заглавной буквы и содержать только латинские буквы.',
            'username.unique' => 'Это имя пользователя уже занято.',

            'email.required' => 'Email обязателен.',
            'email.string' => 'Email должен быть строкой.',
            'email.email' => 'Введите корректный email.',
            'email.unique' => 'Этот email уже зарегистрирован.',

            'password.required' => 'Пароль обязателен.',
            'password.string' => 'Пароль должен быть строкой.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.regex' => 'Пароль должен содержать хотя бы одну заглавную букву, одну строчную букву, одну цифру и один спецсимвол.',

            'password_confirmation.required' => 'Подтверждение пароля обязательно.',
            'password_confirmation.string' => 'Подтверждение пароля должно быть строкой.',
            'password_confirmation.same' => 'Пароль и подтверждение пароля должны совпадать.',

            'birthday.required' => 'Дата рождения обязательна.',
            'birthday.date' => 'Введите корректную дату рождения.',
            'birthday.date_format' => 'Формат даты рождения должен быть ГГГГ-ММ-ДД.',
            'birthday.before_or_equal' => 'Вы должны быть старше 14 лет.',
        ];
    }
}
