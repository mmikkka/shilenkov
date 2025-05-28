<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\ChangeLogResource;
use App\Http\Resources\PermissionCollectionResource;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Services\ChangeLogService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PermissionController extends Controller
{
    /**
     * Получить список всех ролей
     */
    public function index(Request $request): PermissionCollectionResource
    {
        $permissions = Permission::query()->paginate(
            perPage: $request->get('limit', 15),
            page: $request->get('page', 1),
        );

        return new PermissionCollectionResource($permissions);
    }

    /**
     * Создать новую роль
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $permission = Permission::query()->create((array)$dto);

        return response()->json([
            'message' => 'Разрешение успешно создано.',
            'data' => new PermissionResource($permission),
        ], Response::HTTP_CREATED);
    }

    /**
     * Вывести разрешение
     */
    public function show(Permission $permission): PermissionResource
    {
        return new PermissionResource($permission);
    }

    /**
     * Изменить разрешение
     */
    public function update(Permission $permission, UpdatePermissionRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $permission->update((array)$dto);

        return response()->json([
            'message' => 'Разрешение успешно обновлено.',
            'data' => new PermissionResource($permission),
        ]);
    }

    /**
     * Мягкое удаление
     */
    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return response()->json([
            'message' => 'Разрешение успешно удалено',
        ]);
    }

    /**
     * Жёсткое удаление
     */
    public function forceDestroy($id): JsonResponse
    {
        $permission = Permission::withTrashed()->findOrFail($id);
        $permission->forceDelete();

        return response()->json([
            'message' => 'Разрешение полностью удалено из системы',
        ]);
    }

    /**
     * Восстановить удаленную роль
     */
    public function restore($id): JsonResponse
    {
        $permission = Permission::onlyTrashed()->findOrFail($id);
        $permission->restore();

        return response()->json([
            'message' => 'Разрешение успешно восстановлено',
            'data' => new PermissionResource($permission),
        ]);
    }

    public function story($id, ChangeLogService $service): AnonymousResourceCollection
    {
        $logs = $service->getEntityStory(Permission::class, $id);

        return ChangeLogResource::collection($logs);
    }
}
