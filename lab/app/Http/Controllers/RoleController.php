<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleCollectionResource;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    /**
     * Получить список всех ролей
     */
    public function index(Request $request): RoleCollectionResource
    {
        $roles = Role::query()->paginate(
            perPage: $request->get('limit', 15),
            page: $request->get('page', 1)
        );

        return new RoleCollectionResource($roles);
    }

    /**
     * Создать новую роль
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $role = Role::query()->create((array)$dto);

        return response()->json([
            'message' => 'Роль успешно создана.',
            'data' => new RoleResource($role),
        ], Response::HTTP_CREATED);
    }

    /**
     * Получить информацию о конкретной роли
     */
    public function show(Role $role): RoleResource
    {
        return new RoleResource($role);
    }

    /**
     * Обновить данные о роли
     */
    public function update(Role $role, UpdateRoleRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $role->update((array)$dto);

        return response()->json([
            'message' => 'Роль успешно обновлена.',
            'data' => new RoleResource($role),
        ]);
    }

    /**
     * Мягкое удаление роли
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json([
            'message' => 'Роль успешно удалена',
        ]);
    }

    /**
     * Жёсткое удаление роли
     */
    public function forceDestroy($id): JsonResponse
    {
        $role = Role::withTrashed()->findOrFail($id);
        $role->forceDelete();

        return response()->json([
            'message' => 'Роль полностью удалена из системы',
        ]);
    }

    /**
     * Восстановить удаленную роль
     */
    public function restore($id): JsonResponse
    {
        $role = Role::onlyTrashed()->findOrFail($id);
        $role->restore();

        return response()->json([
            'message' => 'Роль успешно восстановлена',
            'data' => new RoleResource($role),
        ]);
    }
}
