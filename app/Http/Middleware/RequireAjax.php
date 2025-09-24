<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAjax
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request is an AJAX request (has X-Requested-With header)
        // or if it's an Inertia request (has X-Inertia header)
        if (!$request->ajax() && !$request->header('X-Inertia')) {
            return response()->json([
                'message' => 'This endpoint only accepts AJAX requests.'
            ], 400);
        }

        return $next($request);
    }
}
