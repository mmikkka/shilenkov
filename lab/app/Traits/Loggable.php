<?php

namespace App\Traits;

use App\DTO\ChangeLogDTO;
use App\Services\ChangeLogService;
use Illuminate\Support\Facades\Auth;

trait Loggable
{
    protected static function bootLoggable(): void
    {
        static::created(function ($model) {
            app(ChangeLogService::class)->logChange(new ChangeLogDTO(
                entityType: get_class($model),
                entityId: $model->id,
                created_by: Auth::id(),
                action: 'create',
                before: null,
                after: $model->getAttributes()
            ));
        });

        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();

            $before = array_intersect_key($original, $changes);

            $userId = auth()->id();

            app(ChangeLogService::class)->logChange(new ChangeLogDTO(
                entityType: get_class($model),
                entityId: $model->id,
                created_by: $userId,
                action: 'update',
                before: $before,
                after: $model->getAttributes()
            ));
        });

        static::deleted(function ($model) {
            app(ChangeLogService::class)->logChange(new ChangeLogDTO(
                entityType: get_class($model),
                entityId: $model->id,
                created_by: Auth::id(),
                action: 'delete',
                before: null,
                after: $model->getAttributes()
            ));
        });
    }
}
