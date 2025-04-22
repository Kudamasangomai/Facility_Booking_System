<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Enum\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Password;


class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        $userdata = $request->validated();
        if (!Auth::attempt($userdata)) {
            return response()->json([
                // 'message' => 'No User Found'
                // changing this to match laravel error messages do it can be displayed to the user
                "errors" => [
                    "email" => ["Invalid credentials"]
                ]
            ], 401);
        }

        $user = User::where('email', $userdata['email'])->first();


        // commented this section out so i could access user details in the UserProfile
        /**
         *limit tokens per user
         *limit tokens per user
         */
        // if ($user->tokens->count() > 0) {
        //     return response()->json([
        //         'user' => $user->name,

        //         'message' => 'Successfully Logged In',


        //     ]);
        // } else {
        return response()->json([
            'user' => $user->name,
            'email' => $user->email,
            'message' => 'Successfully Logged In',
            'token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer Token',

        ]);
    }
    // }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $data['usertype'] = UserType::Customer->value;
        $user = User::create($data);
        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, Response::HTTP_CREATED);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return [
            'message' => 'Logged Out'
        ];
    }


    public function forgotpassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users',
        ]);
        Password::sendResetLink($data);
        return response()->json([
            'message' => 'Reset Password link has been send to your email',
        ]);
    }

    public function passwordreset(Request $request){
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
        ]);
    
        //  check if the token is valid or not
        if (!Password::tokenExists(User::where('email', $request->email)->first(), $request->token)) {
            return response()->json(['message' => 'Invalid token or email.'],
        Response::HTTP_BAD_REQUEST);
        }
    
        // If you are using an API-first approach, just return a success message:(wip)
        return response()->json(['message' => 'Token and email are valid.'], 200);
    }


    public function passwordstore(Request $request)
    {
        $usercreds = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);
        $reset_password_status = Password::reset($usercreds, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(
                ["message" => "Invalid token provided"],
                Response::HTTP_BAD_REQUEST
            );
        }

        return response()->json(["message" => "Password has been successfully changed"]);
    }
}
