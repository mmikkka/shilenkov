<?php

namespace App\Http\Requests;

use App\DTO\RoleDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); //если пользователь авторизован
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max: 50',
                Rule::unique('roles')->whereNull('deleted_at'),
            ],
            'description' => [
                'nullable',
                'string',
                'max: 500',
            ],
            'code' => [
                'required',
                'string',
                'max: 255',
                Rule::unique('roles')->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Требуется указать имя роли.',
            'name.max' => 'Превышено максимальное количество символов.',
            'name.unique' => 'Роль с таким названием уже существует.',

            'description.max' => 'Превышено максимальное количество символов.',

            'code.required' => 'Шифр роли обязателен для заполнения.',
            'code.unique' => 'Роль с таким шифром уже существует.',
            'code.max' => 'Превышено максимальное количество символов.',
        ];
    }

    public function toDTO(): RoleDTO
    {
        return new RoleDTO(
          name: $this->validated('name'),
          description: $this->validated('description'),
          code: $this->validated('code'),
        );
    }
}
