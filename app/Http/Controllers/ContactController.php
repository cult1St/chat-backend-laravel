<?php

namespace App\Http\Controllers;

use App\ApiResponder;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    use ApiResponder;
    public function index(Request $request){
        //get user contacts
        $user = Auth::user();

        $search = $request->get('q');
        $query = Contact::where('inviter_id', $user->id)->with(['inviter', 'invitee']);
        if($search){
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('invitee', function($q) use($search) {
                   return  $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
        }
        $contacts = $query->paginate(request()->take ?? 50);

        $contactsList = ContactResource::collection($contacts);

       return $this->successResponse($contactsList, 'Contacts Data Loaded Successfully');
    }

    public function createContact(Request $request){
        try{
            $validated = $request->validate([
                'name' => "required|string",
                'associated_user_id' => "required|integer|exists:users,id"
            ]);
        }catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
        }

        //create a contact for the respective data passed
        $contact = Contact::create([
            "inviter_id" => Auth::id(),
            "invitee_id" => $validated['associated_user_id'],
            "name" => $validated['name']
        ]);

        return $this->successResponse($contact, "Contact Created SuccessFully");
    }

    public function updateName(Request $request, $id){
        try{
            $validated = $request->validate([
                'name' => "required|string",
            ]);
        }catch(ValidationException $e){
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
        }

        $contact = Contact::find($id);
        if(!$contact){
            return $this->errorResponse('Contact Does not exist', 404);
        }

        $update = $contact->update([
            'name' => $validated['name'],
        ]);
        if($update){
            return $this->successResponse($contact->refresh(), 'Contact Name Updated Successfully');
        }

        return $this->errorResponse('Unable to update Contact. An Error Occurred');
    }
}
