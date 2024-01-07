<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\EmailVerifyNotificationRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ResponseTrait;
    public function user(Request $request)
    {

        return $this->success([
            "data" => $request->user(),
        ]);
    }
    public function register(RegisterRequest $request)
    {

        $arr = $request->validated();
        $arr['password'] = Hash::make($request->password);
        $user = User::create($arr);

        $token = $user->createToken($request->device_name)->plainTextToken;
        return $this->success([
            'message' => "User Register Success!",
            'token' => $token
        ]);
    }
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->failure(
                ['message' => 'The provided credentials are incorrect',]
            );
        }
        $token = $user->createToken($request->device_name)->plainTextToken;
        return $this->success([
            'token' => $token,
            'message' => "User login success",


        ]);
    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();
        return $this->success(
            [
                'message' => "User logout success!",


            ]
        );
    }

    public function emailVerifyNotification(EmailVerifyNotificationRequest $request)
    {


        $user = User::query()->where('email', $request->email)->first();
        if (isset($user)) {
            $user->sendEmailVerificationNotification();
            return $this->success();
        } else {
            return $this->failure(['message' => 'Email not found!']);
        }
    }
    public function emailVerifyAccept(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return response()->json(
            [
                'success' => true
            ],
            200
        );
    }
    public function forgotPassword(ForgotPasswordRequest $request)
    {

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ?   response()->json(
                [
                    'success' => true,
                    'status' => $status
                ],
                200
            )
            : response()->json(
                [
                    'success' => false,
                    'status' => $status
                ],
                400
            );
    }
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'passwordConfirm', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(
                [
                    'success' => true,
                    'status' => $status
                ],
                200
            )
            : response()->json(
                [
                    'success' => false,
                    'status' => $status
                ],
                400
            );;
    }
}
