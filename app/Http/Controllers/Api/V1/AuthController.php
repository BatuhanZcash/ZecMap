<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\PublicLoginRequest;
use App\Http\Requests\PublicRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class AuthController extends Controller
{
    public function login(PublicLoginRequest $request): JsonResponse
    {
        $request->authenticate();

        /** @var User $user */
        $user = Auth::user();
        $user->recordSignIn();

        return $this->respondWithAuthPayload($user);
    }

    public function register(PublicRegisterRequest $request): JsonResponse
    {
        $username = (string) $request->input('username');

        $user = User::query()->create([
            'username' => $username,
            'name' => $username,
            'email' => $username.'@example.com',
            'password' => $request->input('password'),
            'role' => User::ROLE_USER,
            'email_verified_at' => now(),
            'last_sign_in_at' => now(),
        ]);

        return $this->respondWithAuthPayload($user, 201);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->formatUser($request->user()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->password = $request->input('password');
        $user->save();

        return response()->json(['message' => 'Password updated successfully.']);
    }

    private function respondWithAuthPayload(User $user, int $status = 200): JsonResponse
    {
        try {
            return response()->json([
                'data' => $this->authPayload($user),
            ], $status);
        } catch (RuntimeException $exception) {
            if ($exception->getMessage() === 'personal_access_tokens_missing') {
                return response()->json([
                    'message' => __('auth.tokenStoreFailed'),
                    'code' => 'AUTH_TOKEN_STORE_FAILED',
                ], 503);
            }

            throw $exception;
        }
    }

    /**
     * @return array{token: string, user: array<string, mixed>}
     */
    private function authPayload(User $user): array
    {
        try {
            $token = $user->createToken('mobile')->plainTextToken;
        } catch (QueryException $exception) {
            if (str_contains($exception->getMessage(), 'personal_access_tokens')) {
                report($exception);

                throw new RuntimeException('personal_access_tokens_missing', previous: $exception);
            }

            throw $exception;
        }

        return [
            'token' => $token,
            'user' => $this->formatUser($user),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatUser(User $user): array
    {
        return (new UserResource($user))->resolve();
    }
}
