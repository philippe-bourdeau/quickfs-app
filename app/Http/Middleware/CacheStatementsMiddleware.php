<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class CacheStatementsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ticker = $request->get('ticker');

        $cachedEntry = \Illuminate\Support\Facades\Redis::get($ticker);
        if ($cachedEntry) {
            return new JsonResponse(\GuzzleHttp\json_decode($cachedEntry));
        }

        $response = $next($request);

        \Illuminate\Support\Facades\Redis::set(
            $request->get('ticker'),
            $response->getContent(),
            'EX',
            60 * 60 * 24
        );

        return $response;
    }
}
