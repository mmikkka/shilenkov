<?php

namespace App\Http\Requests;

use App\DTO\PermissionDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
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
                'max:50',
                Rule::unique('permissions')->whereNull('deleted_at'),
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions')->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Требуется указать название разрешения.',
            'name.max' => 'Название не должно превышать 50 символов.',
            'name.unique' => 'Разрешение с таким названием уже существует.',

            'description.max' => 'Описание не должно превышать 500 символов.',

            'code.required' => 'Шифр разрешения обязателен для заполнения.',
            'code.unique' => 'Разрешение с таким шифром уже существует.',
            'code.max' => 'Шифр не должен превышать 255 символов.',
        ];
    }

    public function toDTO(): PermissionDTO
    {
        return new PermissionDTO(
            name: $this->validated('name'),
            code: $this->validated('code'),
            description: $this->validated('description')
        );
    }
}
