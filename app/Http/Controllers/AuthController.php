<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserDataResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends Controller
{
    use HttpResponses;

    /**
     * Using Passport Method
     */
    public function login(Request $request, ServerRequestInterface $serverRequest, AccessTokenController $tokenController)
    {
        // Validate incoming data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Use same request to avoid cors/preflight problem
        $passportRequest = $serverRequest->withParsedBody([
            'scope'         => '*',
            'grant_type'    => 'password',
            'username'      => $request->email,
            'password'      => $request->password,
            'client_id'     => config('auth.passport_password_client_id'),
            'client_secret' => config('auth.passport_password_client_secret'),
        ]);

        try {
            // Call Passport token controller directly
            $tokenResponse = $tokenController->issueToken($passportRequest);

            $tokenData = json_decode($tokenResponse->getContent(), true);
            $user = User::where('email', $request->input('email'))->first();

            // Return same structure as old Sanctum login
            return $this->success([
                'user_data'     => new UserDataResource($user),
                'user_email'    => $user->email,
                'user_name'     => $user->name,
                'token'         => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
            ]);
        } catch (OAuthServerException $e) {
            return $this->error('Invalid credentials', 'The provided email or password is incorrect', 401);
        } catch (\Exception $e) {
            return $this->error('Server error', 'Unable to process login request', 500);
        }
    }

    /**
     * Using Sanctum Method
     */
    public function loginSanctum(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('', 'Credentials do not match', 401);
        }

        $user = User::find(Auth::user()->id);
        $token = $user->createToken('appToken')->accessToken;

        return $this->success([
            'user_data'  => new UserDataResource($user),
            'user_email' => $user->email,
            'user_name'  => $user->name,
            'token'      => $token,
        ]);
    }

    public function register(RegisterUserRequest $request)
    {
        $request->validated($request->all());
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->success([
            'user_data'  => new UserDataResource($user),
            'user_email' => $user->email,
            'user_name'  => $user->name,
            'token'      => $user->createToken('appToken')->accessToken,
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::user()) {
            $request->user()->token()->revoke();
        }

        return $this->success([
            'message' => 'You have successfully been logged out.',
        ]);
    }
}
