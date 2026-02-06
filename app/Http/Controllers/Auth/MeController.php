<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MeController extends Controller
{
    /**
     * Get the authenticated user's information.
     */
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'user' => new UserResource(Auth::user())
        ], 200);
    }
}
