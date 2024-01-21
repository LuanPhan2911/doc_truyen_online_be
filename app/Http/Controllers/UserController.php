<?php

namespace App\Http\Controllers;

use App\Enums\StoryUserType;
use App\Http\Requests\StoreStoryMarkingRequest;
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
    public function update(UpdateUserRequest $request)
    {
        $user = $request->user('sanctum');
        $arr = $request->only([
            "name",
            "description",
            "birth_date",
            "gender"
        ]);

        if ($request->hasFile("avatar")) {
            $path = Storage::disk("public")->put('users', $request->file('avatar'));

            $arr['avatar'] = $path;
            if (!empty($user->avatar)) {
                Storage::disk("public")->delete($user->avatar);
            }
        }
        $user->update($arr);

        return $this->success([
            "data" => $user,
        ]);
    }
    public function destroyStoryReading(Request $request, Story $story)
    {
        $user = $request->user('sanctum');
        $user->stories()->wherePivot('type', StoryUserType::READING)
            ->detach($story->id);
        return $this->success();
    }
    public function destroyStoryMarking(Request $request, Story $story)
    {
        $user = $request->user('sanctum');
        $user->stories()->wherePivot('type', StoryUserType::MARKING)
            ->detach($story->id);
        return $this->success();
    }
    public function updateStoryNotifies(Request $request, Story $story)
    {


        $user = $request->user('sanctum');
        $storyNotify = $user->stories()
            ->wherePivot('type', StoryUserType::READING)
            ->where([
                ['stories.id', $story->id],

            ])->first();

        $is_notified = !$storyNotify?->story_user?->notified;
        $user->stories()
            ->wherePivot('type', StoryUserType::READING)
            ->updateExistingPivot($story->id, [
                'notified' => $is_notified
            ]);
        return $this->success(
            [
                'data' => [
                    'action' => $is_notified ? 1 : 0
                ],
            ]
        );
    }
    public function getStoriesReadingPaginate(Request $request)
    {

        $user = $request->user('sanctum');
        $stories_paginate = $user->stories()
            ->wherePivot('type', StoryUserType::READING)
            ->withCount('chapters')
            ->paginate(10);

        $stories_paginate->makeHidden([
            'description'
        ]);

        return $this->success([
            'data' => $stories_paginate
        ]);

        return $this->failure();
    }
    public function getStoriesMarkingPaginate(Request $request)
    {
        $user = $request->user('sanctum');
        $stories_paginate = $user->stories()
            ->wherePivot('type', StoryUserType::MARKING)
            ->withCount('chapters')
            ->paginate(10);

        $stories_paginate->makeHidden([
            'description'
        ]);

        return $this->success([
            'data' => $stories_paginate
        ]);

        return $this->failure();
    }
    public function createStoryMarking(StoreStoryMarkingRequest $request, Story $story)
    {
        $index = $request->get('index') ?? 0;
        $user = $request->user('sanctum');
        $hasStory = $user->stories()
            ->wherePivot('type', StoryUserType::MARKING)
            ->where('stories.id', $story->id)->first();
        if ($hasStory) {
            $user->stories()
                ->wherePivot('type', StoryUserType::MARKING)
                ->updateExistingPivot(
                    $story->id,
                    [
                        "index" => $index,
                        "updated_at" => Date::now(),
                        "type" => StoryUserType::MARKING

                    ]
                );
        } else {
            $user->stories()
                ->wherePivot('type', StoryUserType::MARKING)->attach(
                    $story->id,
                    [
                        "index" => $index,
                        "created_at" => Date::now(),
                        "updated_at" => Date::now(),
                        "type" => StoryUserType::MARKING
                    ],

                );
        }
        return $this->success();
    }
}
