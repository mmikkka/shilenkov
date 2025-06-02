<?php

namespace App\Http\Controllers;

use App\Models\LogRequest;
use App\DTO\LogRequestDTO;
use App\DTO\LogRequestCollectionDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class LogRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Валидация параметров
        $validator = Validator::make($request->all(), [
            'sortBy' => 'array',
            'sortBy.*.key' => 'required|string|in:url,controller,action,status_code,called_at',
            'sortBy.*.order' => 'required|string|in:asc,desc',
            'filter' => 'array',
            'filter.*.key' => 'required|string|in:user_id,status_code,ip_address,user_agent,controller',
            'filter.*.value' => 'required',
            'page' => 'integer|min:1',
            'count' => 'integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $query = LogRequest::query();

        if ($filters = $request->input('filter')) {
            foreach ($filters as $filter) {
                $query->where($filter['key'], $filter['value']);
            }
        }

        if ($sorts = $request->input('sortBy')) {
            foreach ($sorts as $sort) {
                $query->orderBy($sort['key'], $sort['order']);
            }
        } else {
            $query->latest('called_at');
        }

        $perPage = $request->input('count', 10);
        $page = $request->input('page', 1);
        $logs = $query->paginate($perPage, ['*'], 'page', $page);

        $items = $logs->map(fn($log) => [
            'id' => $log->id,
            'url' => $log->url,
            'controller' => $log->controller,
            'action' => $log->action,
            'status_code' => $log->status_code,
            'called_at' => $log->called_at->toDateTimeString(),
        ]);

        $dto = new LogRequestCollectionDTO(
            items: $items->toArray(),
            total: $logs->total(),
            perPage: $logs->perPage(),
            currentPage: $logs->currentPage()
        );

        return response()->json($dto);
    }

    public function show(LogRequest $log): JsonResponse
    {
        $dto = new LogRequestDTO(
            id: $log->id,
            url: $log->url,
            method: $log->method,
            controller: $log->controller,
            action: $log->action,
            requestBody: $log->request_body,
            requestHeaders: $log->request_headers,
            userId: $log->user_id,
            ipAddress: $log->ip_address,
            userAgent: $log->user_agent,
            statusCode: $log->status_code,
            responseBody: $log->response_body,
            responseHeaders: $log->response_headers,
            calledAt: $log->called_at
        );

        return response()->json($dto);
    }

    public function destroy(LogRequest $log): JsonResponse
    {
        $log->delete();
        return response()->json(['message' => 'Лог успешно удалён']);
    }
}
