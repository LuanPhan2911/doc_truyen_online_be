<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use ResponseTrait;
    public function show(User $user)
    {

        return $this->success([
            "data" => $user
        ]);
    }
    public function update(UpdateUserRequest $request, User $user)
    {
        $arr = $request->only([
            "name",
            "description",
            "birth_date",
            "gender"

        ]);
        if ($request->hasFile("avatar")) {
            $path = Storage::disk("public")->put('users', $request->file('avatar'));
            $arr["avatar"] = $path;
            if (!empty($user->avatar)) {
                Storage::disk("public")->delete($user->avatar);
            }
        }
        $user->update($arr);

        return $this->success([
            "data" => $user,
        ]);
    }
}
