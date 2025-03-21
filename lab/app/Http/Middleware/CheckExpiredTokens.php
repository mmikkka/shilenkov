<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckExpiredTokens
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            $user->removeExpiredTokens();
        }

        return $next($request);
    }
}
