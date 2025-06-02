<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class MetadataSheet implements FromArray, WithHeadings, WithTitle
{
    protected Carbon $dateFrom;

    public function __construct(Carbon $dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    public function array(): array
    {
        return [
            ['Тип отчета', 'Системный отчет'],
            ['Дата генерации', now()->toDateTimeString()],
            ['Данные с', $this->dateFrom->toDateTimeString()],
            ['Данные по', now()->toDateTimeString()],
        ];
    }

    public function headings(): array
    {
        return ['Параметр', 'Значение'];
    }

    public function title(): string
    {
        return 'Метаданные';
    }
}
