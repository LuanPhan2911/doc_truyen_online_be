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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ResponseTrait;
    public function user(Request $request)
    {
        $user = $request->user();
        if (empty($user)) {
            return $this->failure();
        }
        return $this->success([
            "data" => $user,
        ]);
    }
    public function register(RegisterRequest $request)
    {
        try {
            $arr = $request->all();
            $arr['password'] = Hash::make($request->password);
            $user = User::create($arr);
            $token = $user->createToken('access_token')->plainTextToken;
            return $this->success([
                'token' => $token,
                'message' => "Register success!",
                "data" => $user,
            ]);
        } catch (\Throwable $th) {
            return $this->failure([]);
            //throw $th;
        }
    }
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);
            $rememberMe = $request->get("rememberMe");
            if (!Auth::attempt($credentials, $rememberMe)) {
                return $this->failure([
                    'error' => [],
                    'message' => "Email or password not found!"
                ]);
            }

            $user = User::where('email', $request->email)->first();

            $token = $user->createToken('access_token')->plainTextToken;
            return $this->success([
                'token' => $token,
                'message' => "User login success",
                "data" => $user,
            ]);
        } catch (\Exception $error) {
            return $this->failure(
                [
                    'error' => $error,
                    'message' => 'User login fail'
                ]
            );
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        Auth::guard('web')->logout();
        $user->tokens()->delete();
        return $this->success(
            [
                'message' => "User logout success!"
            ]
        );
    }

    public function emailVerifyNotification(EmailVerifyNotificationRequest $request)
    {
        try {
            $user = $request->user();
            if (empty($user)) {
                $user = User::query()->where('email', $request->email)->first();
            }
            $user->sendEmailVerificationNotification();
            return $this->success();
        } catch (\Throwable $th) {
            return $this->failure();
        }
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
