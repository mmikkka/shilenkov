<?php

namespace App\Http\Controllers;

use App\DTO\ChangeLogCollectionDTO;
use App\Http\Resources\ChangeLogResource;
use App\Models\ChangeLog;
use App\Services\ChangeLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChangeLogController extends Controller
{
    public function __construct(private readonly ChangeLogService $changeLogService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'entity_type' => 'sometimes|string',
            'entity_id' => 'sometimes|integer'
        ]);

        $query = ChangeLog::query();

        if ($request->has('entity_type')) {
            $query->where('entity_type', $request->entity_type);

            if ($request->has('entity_id')) {
                $query->where('entity_id', $request->entity_id);
            }
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(15);

        return ChangeLogResource::collection($logs);
    }

    public function rollback($logId): JsonResponse
    {
        $model = $this->changeLogService->rollbackChange($logId);

        return response()->json([
            'message' => 'Изменение успешно откачено',
            'data' => $model
        ]);
    }
}
