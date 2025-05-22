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
            $model = $changeLog->entity_type::findOrFail($changeLog->entity_id);

            if ($changeLog->action === 'delete') {
                $model->restore();
            } else {
                $model->fill($changeLog->before);
                $model->save();
            }

            // Логируем откат
            $this->logChange(new ChangeLogDTO(
                entityType: $changeLog->entity_type,
                entityId: $changeLog->entity_id,
                created_by: Auth::id(),
                action: 'rollback',
                before: $model->toArray(),
                after: $changeLog->before
            ));

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
