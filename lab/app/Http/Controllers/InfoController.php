<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfoController extends Controller
{
    public function clientInfo(Request $request): JsonResponse
    {
        $clientInfo = [
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ];

        return response()->json($clientInfo);
    }

    public function serverInfo(): JsonResponse
    {
        return response()->json([
            'php_version' => phpversion(),
        ]);
    }

    public function databaseInfo(): JsonResponse
    {
        $connection = DB::connection()->getPdo();

        return response()->json([
            'driver' => DB::connection()->getDriverName(),
            'database' => DB::connection()->getDatabaseName(),
        ]);
    }
}
