<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Ensure user has a company_id
        if (!Auth::user()->company_id) {
            return response()->json([
                'message' => 'User does not belong to any company.'
            ], 403);
        }

        // Inject company_id into the request for explicit use if needed
        $request->merge([
            'company_id' => Auth::user()->company_id
        ]);

        return $next($request);
    }
}
