<?php

namespace App\Services;

use App\DTO\ChangeLogDTO;
use App\Models\ChangeLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChangeLogService
{
    public function getEntityStory(string $entityType, int $entityId): Collection
    {
        return ChangeLog::query()->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->with('creator:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function rollbackChange(int $changeLogId)
    {
        return DB::transaction(function () use ($changeLogId) {
            $changeLog = ChangeLog::query()->findOrFail($changeLogId);
            $model = $changeLog->entity_type::withTrashed()->findOrFail($changeLog->entity_id);

            if ($changeLog->is_rollbacked) {
                abort(422, "Данный лог уже был откачен");
            }

            if ($changeLog->action === 'delete') {
                $model->restore();
            } else if ($changeLog->action === 'create') {
                $model->delete();
            } else {
                $model->fill($changeLog->before);
                $model->save();
            }

            $changeLog->is_rollbacked = true;
            $changeLog->rollbacked_at = now();

            $changeLog->save();

            return $model;
        });
    }

    public function logChange(ChangeLogDTO $dto): ChangeLog
    {
        return ChangeLog::query()->create([
            'entity_type' => $dto->entityType,
            'entity_id' => $dto->entityId,
            'created_by' => $dto->created_by,
            'action' => $dto->action,
            'before' => $dto->before,
            'after' => $dto->after
        ]);
    }
}
