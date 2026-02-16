<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Middleware\JwtAuth;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
// use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;


class JwtAuth
{
    use HttpResponses; 
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    //     public function handle(Request $request, Closure $next): Response
    // use App\Helpers\JwtHelper;
    // use Closure;

    //verify user token from req.header
    public function handle($request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            // return response()->json(['message' => 'Unauthorized'], 401);
            return $this->error([], 'Unauthorized', 401);
        }

        try {
            $token = trim(str_replace('Bearer', '', $authHeader));
            $decoded = JwtAuth::verify($token);

            $request->attributes->set('jwt', $decoded);
        } catch (\Throwable $e) {
            // return response()->json(['message' => 'Invalid or expired token'], 401);
            return $this->error([], 'Invalid or expired token', 401);

        }

        return $next($request);
    }

    //generate a new token 
    public static function generate(array $payload)
    {
        $payload = array_merge($payload, [
            'iss' => config('app.url'),
            'iat' => time(),
            'exp' => time() + (60 * 15), // 15 minutes
        ]);

        return JWT::encode($payload, config('app.key'), 'HS256');
    }

    //verify user token might not need this
    public static function verify(string $token)
    {
        return JWT::decode($token, new Key(config('app.key'), 'HS256'));
    }

}
