<?php

namespace App\Http\Controllers;

use App\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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

    public function updateDetails(Request $request)
    {
        $user = Auth::user();
        try{
            $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|unique:users,phone,' . $user->id,
            'bio' => 'nullable|string|max:1000',

        ]);
        }catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, $e->errors(),);
        }

        $user->update($validatedData);

        return $this->successResponse($user, 'User details updated successfully.');
    }
}
