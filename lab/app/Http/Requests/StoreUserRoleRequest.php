<?php

namespace App\Http\Requests;

use App\DTO\UserRoleDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRoleRequest extends FormRequest
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
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('user_role')->where(function ($query) {
                    return $query->where('role_id', $this->role_id);
                })
            ],
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Требуется указать ID пользователя.',
            'user_id.exists' => 'Указанный пользователь не существует.',
            'user_id.unique' => 'У пользователя уже есть эта роль.',

            'role_id.required' => 'Требуется указать ID роли.',
            'role_id.exists' => 'Указанная роль не существует.',
        ];
    }

    public function toDTO(): UserRoleDTO
    {
        return new UserRoleDTO(
            user_id: $this->validated('user_id'),
            role_id: $this->validated('role_id')
        );
    }
}
