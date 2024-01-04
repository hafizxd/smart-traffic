<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Transformers\UserCollection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile()
    {
        return composeReply(true, 'Success', new UserCollection(Auth::user()));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'string',
            'profile_picture' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return composeReply(false, 'Validation fails.', [
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ];

        if (isset($request->profile_picture)) {
            $fileName = time() . '_' . $request->profile_picture->getClientOriginalName();
            $request->profile_picture->storeAs('users', $fileName, 'public');
            $updateData['profile_picture'] = $fileName;
        }

        Auth::user()->update($updateData);

        return composeReply(true, 'Success', new UserCollection(Auth::user()));
    }
}
