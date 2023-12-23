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
    public function getStoriesReading()
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
            if ($stories_auth->isEmpty()) {
                $stories = $stories_not_auth;
            }
        } else {
            $stories = $stories_not_auth;
        }
        return $this->success([
            "data" => $stories
        ]);
    }
    public function destroyStoryReading(Story $story)
    {
        $user = Auth::user();
        $user->stories()->updateExistingPivot(
            $story->id,
            ["reading_deleted_at" => Date::now()]
        );
        return $this->success();
    }
    public function destroyStoryMarking(Story $story)
    {
        $user = Auth::user();
        $user->stories()->updateExistingPivot(
            $story->id,
            ["marking_deleted_at" => Date::now()]
        );
        return $this->success();
    }
    public function updateStoryMarking(Story $story, $index)
    {
        $user = Auth::user();
        $hasStory = $user->stories->where('id', $story->id)->count() > 0;
        if ($hasStory) {
            $user->stories()->updateExistingPivot(
                $story->id,
                [
                    "marked" => 1,
                    "marked_index" => $index,
                    "updated_at" => Date::now()
                ]
            );
        } else {
            $user->stories()->attach(
                $story->id,
                [
                    "marked_index" => $index,
                    "marked" => 1,
                    "index" => 1,
                    "reading_deleted_at" => Date::now(),
                    "created_at" => Date::now(),
                    "updated_at" => Date::now()
                ],

            );
        }
        return $this->success();
    }
    public function updateNotifies(User $user, Story $story)
    {

        $user = User::find(Auth::id());
        $storyNotify = $user->stories()->where('stories.id', $story->id)->first();
        $user->stories()->updateExistingPivot($story->id, [
            'notified' => !boolval($storyNotify?->pivot?->notified)
        ]);
        return $this->success();
    }
    public function getStoriesReadingPaginate()
    {
        if (Auth::check()) {
            $user = User::find(Auth::id());
            $stories_paginate = $user->stories()
                ->wherePivotNull('reading_deleted_at')
                ->withCount('chapters')
                ->paginate(10);
            $stories_paginate->makeHidden([
                'description'
            ]);
            return $this->success([
                'data' => $stories_paginate
            ]);
        }
        return $this->failure();
    }
    public function getStoriesMarkingPaginate()
    {
        if (Auth::check()) {
            $user = User::find(Auth::id());
            $stories_paginate = $user->stories()
                ->wherePivot('marked', 1)
                ->withCount('chapters')
                ->paginate(10);
            $stories_paginate->makeHidden([
                'description'
            ]);
            return $this->success([
                'data' => $stories_paginate
            ]);
        }
        return $this->failure();
    }
    public function notifies()
    {
        $user = User::find(Auth::id());
        $user->load([
            'chapters:id,name,index,story_id' => [
                'story:id,name,slug,avatar'
            ]
        ]);
        $notifies = $user->chapters
            ->where('pivot.is_seen', 0)
            ->sortBy([
                ['pivot.created_at', 'desc']
            ])
            ->values()
            ->take(5);

        $notifies->makeHidden([
            'pivot',
            'story_id'
        ]);
        return $this->success([
            'data' => $notifies
        ]);
    }
}
