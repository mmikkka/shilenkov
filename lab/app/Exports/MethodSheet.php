<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class MethodSheet implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    protected Collection $methods;

    public function __construct(Collection $methods)
    {
        $this->methods = $methods;
    }

    public function collection()
    {
        return $this->methods;
    }

    public function headings(): array
    {
        return [
            'HTTP Метод',
            'Количество вызовов',
            'Последний вызов',
        ];
    }

    public function map($method): array
    {
        return [
            $method->method,
            $method->count,
            $method->last_operation,
        ];
    }

    public function title(): string
    {
        return 'Методы';
    }
}
