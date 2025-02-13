<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfoController extends Controller
{
    public function clientInfo(Request $request)
    {
        // Получение информации о клиенте
        $clientInfo = [
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ];

        return response()->json($clientInfo);
    }
    public function serverInfo()
    {
        return response()->json([
            'php_version' => phpversion(),
        ]);
    }

    public function databaseInfo() {
        $connection = DB::connection()->getPdo();
        return response()->json([
            'driver' => DB::connection()->getDriverName(),
            'database' => DB::connection()->getDatabaseName(),
        ]);
    }
}
