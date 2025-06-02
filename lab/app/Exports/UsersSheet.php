<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class UsersSheet implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    protected Collection $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        return $this->users;
    }

    public function headings(): array
    {
        return [
            'Пользователь',
            'Запросов',
            'Изменений',
            'Авторизаций',
            'Последняя операция',
        ];
    }

    public function map($user): array
    {
        return [
            $user['user'],
            $user['requests_count'],
            $user['changes_count'],
            $user['authorizations_count'],
            $user['last_operation'],
        ];
    }

    public function title(): string
    {
        return 'Пользователи';
    }
}
