<?php

namespace App\Http\Requests;

use App\DTO\UserRoleDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property mixed $user_role
 */
class UpdateUserRoleRequest extends FormRequest
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
        $userRole = $this->route('user_role');

        return [
            'user_id' => [
                'sometimes',
                'integer',
                'exists:users,id',
                Rule::unique('user_role')
                    ->where(function ($query) use ($userRole) {
                        return $query->where('role_id', $this->input('role_id', $userRole->role_id));
                    })
                    ->ignore($userRole->id)
            ],
            'role_id' => [
                'sometimes',
                'integer',
                'exists:roles,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'Указанный пользователь не существует.',
            'user_id.unique' => 'У пользователя уже есть эта роль.',

            'role_id.exists' => 'Указанная роль не существует.',
        ];
    }

    public function toDTO(): UserRoleDTO
    {
        return new UserRoleDTO(
            user_id: $this->validated('user_id', $this->user_role->user_id),
            role_id: $this->validated('role_id', $this->user_role->role_id)
        );
    }
}
