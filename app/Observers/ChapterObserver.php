<?php

namespace App\Observers;

use App\Models\Chapter;
use App\Models\Story;

class ChapterObserver
{
    /**
     * Handle the Chapter "created" event.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return void
     */
    public function created(Chapter $chapter)
    {
        // $story = Story::find($chapter->story_id);
        // $story->load("users");
        // $users_id = $story->users()->wherePivot("notified", 1)->get()->pluck("id");
        // $collection = collect($users_id)->map(function ($userId) use ($chapter) {
        //     return [
        //         "user_id" => $userId,
        //         "index" => $chapter->index,
        //     ];
        // })->toArray();
        // $story->notifies()->createMany($collection);
    }

    /**
     * Handle the Chapter "updated" event.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return void
     */
    public function updated(Chapter $chapter)
    {
        //
    }

    /**
     * Handle the Chapter "deleted" event.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return void
     */
    public function deleted(Chapter $chapter)
    {
        //
    }

    /**
     * Handle the Chapter "restored" event.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return void
     */
    public function restored(Chapter $chapter)
    {
        //
    }

    /**
     * Handle the Chapter "force deleted" event.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return void
     */
    public function forceDeleted(Chapter $chapter)
    {
        //
    }
}
