<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidRequestException;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserFilter
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
        if (!Str::startsWith($request->route()->getPrefix(), 'api/Server')) {
            $userName = $request->input('operator', 'admin');
            $user = User::query()->where('username', $userName)->first();
            if (!$user) {
                throw new InvalidRequestException('人事系统未同步此账号，请找IT确认');
            }
            Auth::login($user);
        }
        return $next($request);
    }
}
