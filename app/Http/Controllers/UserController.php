<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

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
    public function stories(User $user)
    {

        // $user = request()->user();
        if (!empty($user)) {;
            $user->load([
                "stories:id,name,avatar,slug"
            ]);
            $stories = $user->stories->loadCount("chapters");
            return $this->success([
                "data" => $stories
            ]);
        }
        return $this->failure([
            "message" => "User not found"
        ]);
    }
    public function notifies(User $user)
    {
        if (!empty($user)) {;
            $user->load([
                "notifies" => [
                    "story:id,name,avatar,slug"
                ]
            ]);
            $notifies = $user->notifies;
            $new_notifies_count = $user->notifies()->count('active', false);
            return $this->success([
                "data" => [
                    "notifies" => $notifies,
                    "new_notifies_count" => $new_notifies_count
                ],

            ]);
        }
        return $this->failure([
            "message" => "User not found"
        ]);
    }
}
