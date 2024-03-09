<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(Request $request) {
        // NOTE: phone must be unique for every User; unique param checks this
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|unique:User|string',
            'document_number' => 'required|string|min:10|max:10',
            'password' => 'required|string'
        ]);

        $user = new User();
        $user->setRawAttributes([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone' => $request->input('phone'),
            'document_number' => $request->input('document_number'),
            'password' => $request->input('password'),
        ]);

        $user->save();

        return response()->noContent();
    }

    public function authentication(Request $request) {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::query()->where('phone', $request->input('phone'))->first();

        // TODO: move error to App/Exceptions later
        if($user == null) {
            return response()->json([
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized',
                    'errors' => [
                        'phone' => [ 'phone or password incorrect' ]
                    ]
                ]
            ], 401);
        }

        // NOTE: generates random token of 32 symbols
        $user->api_token = Str::random(32);
        $user->save();

        return response()->json([
            'data' => [
                'token' => $user->api_token
            ],
        ], 200);
    }

    /**
     * Fetch current user
     * @return \Illuminate\Http\JsonResponse
     */
    public function current() {
        $user = auth()->user();

        return response()->json([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'document_number' => $user->document_number
        ], 200);
    }
}
