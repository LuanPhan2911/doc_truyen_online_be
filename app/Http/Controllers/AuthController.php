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
            'email' => $user->email,
        ]);
    }
    public function register(RegisterRequest $request)
    {
        try {
            $arr = $request->all();
            $arr['password'] = Hash::make($request->password);
            $user = User::create($arr);
            $token = $user->createToken('MyApp')->plainTextToken;
            return $this->success($token);
        } catch (\Throwable $th) {
            return $this->failure('fail');
            //throw $th;
        }
    }
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return $this->failure('not found');
            }

            $user = User::where('email', $request->email)->first();

            $token = $user->createToken('authToken')->plainTextToken;
            return $this->success([
                'token' => $token
            ]);
        } catch (\Exception $error) {
            return $this->failure();
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        Auth::guard('web')->logout();
        $user->tokens()->delete();
        return $this->success(
            [
                // 'user' => $user,
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
