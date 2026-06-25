<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'company_slug' => ['required', 'string'],
            'email'        => ['required', 'email'],
            'password'     => ['required', 'string'],
            'device_name'  => ['sometimes', 'string', 'max:100'],
        ]);

        $user = User::whereHas('company', function ($q) use ($request) {
            $q->where('slug', $request->company_slug)->where('is_active', true);
        })
            ->where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->update(['last_seen_at' => now()]);

        $tokenName = $request->input('device_name', 'api-token');
        $token = $user->createToken($tokenName, ['*'], now()->addHours(24));

        return response()->json([
            'token'   => $token->plainTextToken,
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'company_id' => $user->company_id,
                'skill_tags' => $user->skill_tags,
                'timezone'   => $user->timezone,
            ],
            'company' => [
                'id'           => $user->company->id,
                'name'         => $user->company->name,
                'slug'         => $user->company->slug,
                'plan'         => $user->company->plan,
                'timezone'     => $user->company->timezone,
                'feature_flags' => $user->company->feature_flags,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('company');

        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'company_id' => $user->company_id,
            'skill_tags' => $user->skill_tags,
            'timezone'   => $user->timezone,
            'company'    => [
                'id'            => $user->company->id,
                'name'          => $user->company->name,
                'slug'          => $user->company->slug,
                'plan'          => $user->company->plan,
                'max_agents'    => $user->company->max_agents,
                'max_channels'  => $user->company->max_channels,
                'feature_flags' => $user->company->feature_flags,
            ],
        ]);
    }
}
