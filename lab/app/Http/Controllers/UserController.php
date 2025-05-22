<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChangeLogResource;
use App\Http\Resources\UserCollectionResource;
use App\Http\Resources\UserWithRolesResource;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Services\ChangeLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(Request $request): UserCollectionResource
    {
        $users = User::query()->paginate(
            perPage: $request->get('perPage', 15),
            page: $request->get('page', 1)
        );

        return new UserCollectionResource($users);
    }

    public function showWithRoles(User $user): UserWithRolesResource
    {
        $user->roles();

        return new UserWithRolesResource($user);
    }

    public function attachRole(User $user, Role $role): JsonResponse
    {
        $user->roles()->attach($role->id);

        return response()->json([
            'message' => 'Роль успешно добавлена',
            'data' => new UserWithRolesResource($user),
        ]);
    }

    public function detachRole(User $user, Role $role): JsonResponse
    {
        UserRole::query()
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->update(['deleted_at' => now()]);

        return response()->json([
            'message' => 'Роль успешно удалена',
            'data' => new UserWithRolesResource($user),
        ]);
    }

    public function forceDetachRole(User $user, Role $role): JsonResponse
    {
        $user->roles()->detach($role);

        return response()->json([
            'message' => 'Роль успешно удалена',
            'data' => new UserWithRolesResource($user),
        ]);
    }

    public function restoreRole(User $user, Role $role): JsonResponse
    {
        $role = UserRole::onlyTrashed()
            ->where('user_id', $user->id)
            ->where('role_id', $role->id);
        $role->update(['deleted_at' => null]);

        return response()->json([
            'message' => 'Роль восстановлена',
            'data' => new UserWithRolesResource($user),
        ]);
    }

    public function story($id, ChangeLogService $service): AnonymousResourceCollection
    {
        $logs = $service->getEntityStory(User::class, $id);

        return ChangeLogResource::collection($logs);
    }
}
