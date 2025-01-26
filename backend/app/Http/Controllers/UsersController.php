<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 422]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        //$token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Successfully user Registered!',
            'user' => $user,
            //'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|max:10'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Invalid email or password',
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Invalid email or password',
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login Successfully!',
            'user' => $user->makeHidden(['password']),
            'token' => $token,
        ], 201);
    }


    public function dashboard(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'Token is expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        return response()->json([
            'user' => $user,
            'message' => 'Welcome to your dashboard'
        ]);
    }

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json([
                    'error' => 'Token not provided',
                ], 401);
            }

            JWTAuth::invalidate($token);

            return response()->json([
                'message' => 'Logout Successfully',
            ], 201);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'error' => 'Falied to Logout Invalid',
            ], 401);
        }
    }

    public function getAllUsers()
    {
        // Obtener todos los usuarios con sus tareas asociadas
        $users = User::with(['tasks' => function ($query) {
            $query->withPivot('status', 'created_at', 'updated_at'); // Incluye datos de la tabla pivot
        }])->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'users' => $users,
        ]);
    }
}
