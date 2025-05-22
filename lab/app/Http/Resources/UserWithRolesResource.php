<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserWithRolesResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'username' => $this->resource->username,
            'email' => $this->resource->email,
            'birthday' => $this->resource->birthday->format('Y-m-d'),
            'roles' => $this->resource->roles->map(function ($role) {
                return new RoleResource($role);
            }),
        ];
    }
}
