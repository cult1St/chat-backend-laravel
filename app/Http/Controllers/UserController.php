<?php

namespace App\Http\Controllers;

use App\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponder;
    public function details()
    {
        $user = Auth::user();
        // Get the current access token from Sanctum
        $user->token = $user->currentAccessToken()->plainTextToken ?? null;
        return $this->successResponse($user);
    }
}
