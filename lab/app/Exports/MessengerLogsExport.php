<?php

namespace App\Exports;

use App\Models\MessengerLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MessengerLogsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $days;

    public function __construct($days = 1)
    {
        $this->days = $days;
    }

    public function query()
    {
        return MessengerLog::with(['user', 'messenger'])
            ->where('created_at', '>=', now()->subDays($this->days))
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Дата',
            'Пользователь',
            'Мессенджер',
            'Сообщение',
            'Статус',
            'Попытка',
            'ID записи'
        ];
    }

    public function map($log): array
    {
        return [
            $log->created_at->format('Y-m-d H:i:s'),
            $log->user->username ?? 'Неизвестно',
            $log->messenger->name ?? 'Неизвестно',
            $log->message,
            $log->status ? 'Успешно' : 'Ошибка',
            $log->attempt_number,
            $log->id
        ];
    }
}
