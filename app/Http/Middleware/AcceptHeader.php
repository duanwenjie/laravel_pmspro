<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AcceptHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        if ($request->has('data.pageNumber')) {
            $request->merge([
                'perPage' => $request->input('data.pageData', 20), //每页多少条
                'page'    => $request->input('data.pageNumber', 1)//当前页
            ]);
        }
        return $next($request);
    }
}
