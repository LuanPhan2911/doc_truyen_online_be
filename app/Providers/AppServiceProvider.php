<?php

namespace App\Providers;

use App\Models\Chapter;
use App\Models\Story;
use App\Observers\ChapterObserver;
use App\Observers\StoryObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::enforceMorphMap([
            'user' => 'App\Models\User',
            'comment' => 'App\Models\Comment',
            "story" => 'App\Models\Story',
        ]);
        // Story::observe(StoryObserver::class);
        Chapter::observe(ChapterObserver::class);
    }
}
