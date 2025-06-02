<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class EntitiesSheet implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    protected Collection $entities;

    public function __construct(Collection $entities)
    {
        $this->entities = $entities;
    }

    public function collection()
    {
        return $this->entities;
    }

    public function headings(): array
    {
        return [
            'Тип сущности',
            'Количество изменений',
            'Последнее изменение',
        ];
    }

    public function map($entity): array
    {
        return [
            $entity->entity_type,
            $entity->count,
            $entity->last_operation,
        ];
    }

    public function title(): string
    {
        return 'Сущности';
    }
}
