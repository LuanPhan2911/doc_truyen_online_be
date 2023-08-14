<?php

namespace App\Observers;

use App\Models\Notify;
use App\Models\Story;

class StoryObserver
{
    /**
     * Handle the Story "created" event.
     *
     * @param  \App\Models\Story  $story
     * @return void
     */
    public function created(Story $story)
    {
        //
    }

    /**
     * Handle the Story "updated" event.
     *
     * @param  \App\Models\Story  $story
     * @return void
     */
    public function updated(Story $story)
    {

        // $story->load("users");
        // $users_id = $story->users()->wherePivot("notified", 1)->get()->pluck("id");
        // $collection = collect($users_id)->map(function ($userId) {
        //     return [
        //         "user_id" => $userId,
        //     ];
        // })->toArray();
        // $story->notifies()->createMany($collection);
    }

    /**
     * Handle the Story "deleted" event.
     *
     * @param  \App\Models\Story  $story
     * @return void
     */
    public function deleted(Story $story)
    {
        //
    }

    /**
     * Handle the Story "restored" event.
     *
     * @param  \App\Models\Story  $story
     * @return void
     */
    public function restored(Story $story)
    {
        //
    }

    /**
     * Handle the Story "force deleted" event.
     *
     * @param  \App\Models\Story  $story
     * @return void
     */
    public function forceDeleted(Story $story)
    {
        //
    }
}
