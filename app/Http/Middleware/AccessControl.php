<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AccessControl
{    
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('jwt_token');

        if ($token) {
            try {
                $user = JWTAuth::setToken($token)->authenticate();
            } catch (\Exception $e) {
                return $this->redirectAndForgetCookie();
            }
            if($user->is_admin == 1 || $user->is_admin == 2 && $user->divisi_id == 1 || $user->is_admin == 2 && $user->divisi_id == 2){
                return $next($request);
            } else {
                return $this->redirectAndForgetCookie();
            }
        }

        return $this->redirectAndForgetCookie();
    }

    private function redirectAndForgetCookie()
    {
        return Redirect::to('login')->withCookie(cookie()->forget('jwt_token'));
    }

}
