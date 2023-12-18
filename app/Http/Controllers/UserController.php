<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Story;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
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
            // Cloudinary::uploadApi();
            // $uploadedAvatar = $request->file('avatar')->storeOnCloudinary('stop_truyen');
            // $arr["avatar"] = $uploadedAvatar->getPublicId();
            $arr['avatar'] = $path;
            if (!empty($user->avatar)) {
                Storage::disk("public")->delete($user->avatar);
                // Cloudinary::destroy($user->getRawOriginal('avatar'));
            }
        }
        $user->update($arr);

        return $this->success([
            "data" => $user,
        ]);
    }
    public function storiesReading()
    {

        $user = request()->user();
        $stories = null;
        $stories_not_auth = Story::query()
            ->withCount('chapters')
            ->latest()
            ->limit(5)
            ->get();
        if (!empty($user)) {;
            $user->load([
                "stories:id,name,avatar,slug,updated_at"
            ]);

            $stories_auth = $user->stories
                ->loadCount('chapters')
                ->whereNull('pivot.reading_deleted_at')
                ->sortBy([
                    ['pivot.updated_at', 'desc']
                ])
                ->values()
                ->take(5);
            $stories = $stories_auth;
            if (empty($stories_auth)) {
                $stories = $stories_not_auth;
            }
        } else {
            $stories = $stories_not_auth;
        }
        return $this->success([
            "data" => $stories
        ]);
    }
    public function destroyReading(Story $story)
    {
        $user = Auth::user();
        $user->stories()->syncWithoutDetaching([$story->id => [
            "reading_deleted_at" => Date::now()
        ]]);
        return $this->success();
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
