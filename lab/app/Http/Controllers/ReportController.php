<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateReportJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Генерирует отчет и отправляет его администраторам
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateReport(Request $request): JsonResponse
    {
        GenerateReportJob::dispatch();

        return response()->json([
            'message' => 'Задача генерации отчета поставлена в очередь'
        ], 202);
    }
}
