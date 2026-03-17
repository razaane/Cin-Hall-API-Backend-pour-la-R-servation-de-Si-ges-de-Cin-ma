<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show()
    {
        return response()->json(auth('api')->user());
    }

    public function update(Request $request)
    {
        $user = auth('api')->user();

        $data = $request->validate([
            'name'   => 'sometimes|string',
            'email'  => 'sometimes|email|unique:users,email,' . $user->id,
            'avatar' => 'sometimes|string',
        ]);

        $user->update($data);

        return response()->json($user);
    }

    public function destroy()
    {
        $user = auth('api')->user();
        auth('api')->logout();
        $user->delete();

        return response()->json([
            'message' => 'Account deleted'
        ]);
    }

    public function index()
    {
        return response()->json(\App\Models\User::all());
    }
}