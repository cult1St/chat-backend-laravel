<?php

namespace App\Http\Controllers;

use App\ApiResponder;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    use ApiResponder;

    public function login(LoginRequest $loginRequest)
    {
        try {
            //authenticate request
            try {
                $loginRequest->authenticate();
            } catch (Exception $e) {
                return $this->errorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user = Auth::user();
            $user->update(['last_login_at' => now()]);
            //create token for the user
            $user->token = $user->createToken('auth_token')->plainTextToken;
            return $this->successResponse($user, 'User logged in successfully');

        } catch (Exception $e) {
            return $this->errorResponse('Login failed', 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:20|unique:users',
                'password' => 'required|string|min:8',
            ]);

        }catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, $e->errors());
        }
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'last_login_at' => now(),
            'is_active' => true,
            'last_login_ip' => $request->ip(),
        ]);

        //create token for the user
        $user->token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse($user, 'User registered successfully', 201);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'User logged out successfully');
    }
}
