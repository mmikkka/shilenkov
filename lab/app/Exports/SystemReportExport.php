<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SystemReportExport implements WithMultipleSheets
{
    protected Collection $methodsRating;
    protected Collection $entitiesRating;
    protected Collection $usersRating;
    protected Carbon $dateFrom;

    public function __construct($methodsRating, $entitiesRating, $usersRating, Carbon $dateFrom)
    {
        $this->methodsRating = collect($methodsRating);
        $this->entitiesRating = collect($entitiesRating);
        $this->usersRating = collect($usersRating);
        $this->dateFrom = $dateFrom;
    }

    public function sheets(): array
    {
        return [
            new MethodSheet($this->methodsRating),
            new EntitiesSheet($this->entitiesRating),
            new UsersSheet($this->usersRating),
            new MetadataSheet($this->dateFrom),
        ];
    }
}
