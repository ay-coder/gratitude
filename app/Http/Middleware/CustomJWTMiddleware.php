<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Http\Middleware\BaseJWTMiddleware;

class CustomJWTMiddleware extends BaseJWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $invalidTokenCode = 200;
        $invalidDataCode  = 999;

        if (! $token = $this->auth->setRequest($request)->getToken()) {
            return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
        }

        try 
        {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) 
        {
            $response = [
                'success'   => false,
                'status'    => false,
                'code'      => $invalidDataCode,
                'message'   => "Session Expired!"
            ];
            return response()->json(
                $response, 200
            );
        } catch (JWTException $e)
        {
            $response = [
                'success'   => false,
                'status'    => false,
                'code'      => $invalidDataCode,
                'message'   => "Session Expired!"
            ];
            return response()->json(
                $response, 200
            );
        }

        if (! $user)
        {
            $respond = [
                'success'   => false,
                'message'   => 'Opps, User not Found !'
            ];

            return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }
}
