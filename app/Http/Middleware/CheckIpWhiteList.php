<?php

namespace App\Http\Middleware;

use App\Models\IpWhiteList;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIpWhiteList
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $ipAddress = $request->ip();

        $Ipwhitelists = IpWhiteList::where('ip_address', $ipAddress)->first();

        if(!$Ipwhitelists){
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. You are not authorized to access this resource.',
                'error' => 'ACCESS_DENIED'
            ], 403);
        }

        return $next($request);
    }
}
