<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChangeLogResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'action' => $this->resource->action,
            'created_at' => $this->resource->created_at->toDateTimeString(),
            'created_by' => [
                'id' => $this->resource->creator->id,
                'name' => $this->resource->creator->name
            ],
            'is_rollbacked' => $this->resource->is_rollbacked,
            'changes' => $this->getChangedFields()
        ];
    }

    protected function getChangedFields(): array
    {
        if (empty($this->before) || empty($this->after)) {
            return [];
        }

        $changed = [];

        foreach ($this->after as $field => $value) {
            if (!array_key_exists($field, $this->before) || $this->before[$field] !== $value) {
                $changed[$field] = [
                    'before' => $this->before[$field] ?? null,
                    'after' => $value
                ];
            }
        }

        return $changed;
    }
}
