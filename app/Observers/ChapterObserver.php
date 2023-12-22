<?php

namespace App\Observers;

use App\Models\Chapter;
use App\Models\Story;
use Illuminate\Support\Facades\Date;

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
        $story = Story::find($chapter->story_id);
        $users_id = $story->users()->wherePivot('notified', 1)->pluck('users.id');

        $notifies = collect($users_id)->map(function ($user_id) {
            return [
                'user_id' => $user_id,
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ];
        });
        $chapter->users()->attach($notifies);
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
