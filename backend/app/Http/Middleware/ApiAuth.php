<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class ApiAuth
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['code' => 401, 'message' => '请先登录'], 401);
        }

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['code' => 401, 'message' => '登录已过期，请重新登录'], 401);
        }

        if (!$user->status) {
            return response()->json(['code' => 401, 'message' => '账号已被禁用，请联系管理员'], 401);
        }

        auth()->setUser($user);

        return $next($request);
    }
}
