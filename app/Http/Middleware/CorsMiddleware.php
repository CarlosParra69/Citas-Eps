<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CORSMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-User-Guard, Accept');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        // Handle preflight requests
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200)->withHeaders([
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS, PATCH',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-User-Guard, Accept',
                'Access-Control-Allow-Credentials' => 'true',
            ]);
        }

        return $response;
    }
}