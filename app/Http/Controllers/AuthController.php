<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(path: '/api/register', summary: 'Register a new user',tags: ["Auth"])] 
    #[OA\Parameter(name: 'name', in: 'query', required: true, schema: new OA\Schema(type: 'string'))] 
    #[OA\Parameter(name: 'email', in: 'query', required: true, schema: new OA\Schema(type: 'string'))] 
    #[OA\Parameter(name: 'password', in: 'query', required: true, schema: new OA\Schema(type: 'string'))] 
    #[OA\Parameter(name: 'password_confirmation', in: 'query', required: true, schema: new OA\Schema(type: 'string'))] 
    #[OA\Response(response: 201, description: 'User registered successfully')]
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user'
        ]);


        return response()->json([
            'user'  => $user,
            'type'  => 'bearer'
        ], 201);
    }

    #[OA\Post(path: '/api/login', summary: 'login a user',tags: ["Auth"])] 
    #[OA\Parameter(name: 'email', in: 'query', required: true, schema: new OA\Schema(type: 'string'))] 
    #[OA\Parameter(name: 'password', in: 'query', required: true, schema: new OA\Schema(type: 'string'))] 
    #[OA\Response(response: 201, description: 'User logged in successfully')]
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        return response()->json([
            'user'  => auth('api')->user(),
            'token' => $token,
            'type'  => 'bearer',
        ]);
    }

    #[OA\Post(path: "/api/logout",summary: "Logout the authenticated user",tags: ["Auth"], security: [["bearerAuth" => []]])]
    #[OA\Response(response: 200,description: "User logged out successfully")]
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    #[OA\Post(path: "/api/refresh",summary: "Refresh JWT token",tags: ["Auth"], security: [["bearerAuth" => []]])]
    #[OA\Response(response: 200,description: "Token refreshed successfully")]
    public function refresh()
    {
        $token = auth('api')->refresh();

        return response()->json([
            'token' => $token,
            'type'  => 'bearer',
        ]);
    }
} 