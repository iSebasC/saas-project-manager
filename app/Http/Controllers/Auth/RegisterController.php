<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Handle a registration request.
     * 
     * This endpoint creates a new company and its owner user in a single transaction.
     * This ensures proper multi-tenant setup from the start.
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        try {
            // Execute registration in a database transaction
            $result = DB::transaction(function () use ($request) {
                // 1. Create the company
                $company = Company::create([
                    'name' => $request->company_name,
                    'slug' => Str::slug($request->company_name) . '-' . Str::random(6),
                ]);

                // 2. Create the owner user
                $user = User::create([
                    'company_id' => $company->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'owner',
                ]);

                // 3. Generate authentication token
                $token = $user->createToken('api-token')->plainTextToken;

                return [
                    'user' => $user,
                    'company' => $company,
                    'token' => $token,
                ];
            });

            return response()->json([
                'message' => 'Registration successful.',
                'user' => new UserResource($result['user']),
                'company' => [
                    'id' => $result['company']->id,
                    'name' => $result['company']->name,
                    'slug' => $result['company']->slug,
                ],
                'token' => $result['token'],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
