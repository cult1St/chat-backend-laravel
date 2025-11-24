<?php

namespace App\Http\Controllers;

use App\ApiResponder;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SearchController extends Controller
{
    use ApiResponder;

    public function searchByPhone(Request $request)
    {
        try {
            $validated = $request->validate([
                'phone' => "required|string|min:10"
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
        }

        $phone = $validated['phone'];
        $authId = Auth::id();


        $user = User::where('phone', 'like', "%{$phone}")->first();

        // If user not found early exit
        if (!$user) {
            return $this->errorResponse('No User with this Phone Found', 404);
        }

        // Prevent self lookup
        if ($user->id === $authId) {
            return $this->errorResponse('Cannot Save Yourself as your Contact', 422);
        }

        // Efficient existence check (no model hydration)
        $alreadyExists = Contact::where('inviter_id', $authId)
                                ->where('invitee_id', $user->id)
                                ->exists();

        if ($alreadyExists) {
            return $this->errorResponse('This User has already been added to your contacts', 422);
        }

        return $this->successResponse($user, 'User Found Successfully');
    }
}
