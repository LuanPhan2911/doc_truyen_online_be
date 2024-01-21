<?php

namespace App\Models;

use App\Enums\GenreType;
use App\Enums\TypeCommentEnum;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Story extends Model
{
    use Sluggable;
    use HasFactory;
    public $guarded = [];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ],
        ];
    }
    public function getGenreAttribute()
    {
        return $this->genres()
            ->where('type', GenreType::CATEGORY)
            ->first()
            ->only(['id', 'name']);
    }
    public function getCommentsCountAttribute()
    {
        return Comment::query()
            ->whereHasMorph(
                'commentable',
                [Story::class],
                function ($query) {
                    $query->where('id', $this->id);
                }
            )->count();
    }
    public function getReactionSummaryAttribute()
    {
        $chapters = Chapter::whereStoryId($this->id)->get();
        $reactionsSummary = collect([]);
        foreach ($chapters as $chapter) {
            $reactionsSummary->push($chapter->getReactionsSummary());
        }
        $reactionsSummary = $reactionsSummary
            ->flatten()
            ->groupBy('name')
            ->map(function ($reaction) {
                return collect($reaction)->pluck('count')->sum();
            });

        $reactions = [];
        foreach ($reactionsSummary as $key => $value) {
            $reactions[] = [
                'name' => $key,
                'count' => $value
            ];
        }
        return $reactions;
    }
    public function getRateStoryAttribute()
    {
        // $comments = Comment::query()
        //     ->whereType(TypeCommentEnum::RATING)
        //     ->whereHasMorph(
        //         'commentable',
        //         [Story::class],
        //         function ($query) {
        //             $query->where('id', $this->id);
        //         }
        //     )->get();
        // $rateStory = collect();
        // $rate = [
        //     'characteristic',
        //     'plot',
        //     'world_building',
        //     'quality_convert'
        // ];
        // foreach ($comments as $comment) {
        //     $rateStory->push($comment->rateStory->only(
        //         $rate
        //     ));
        // }
        // $avgStory = [];
        // foreach ($rate as $value) {
        //     $avgStory[$value] = $rateStory->avg($value);
        // };

        // return $avgStory;
    }
    public function getChapterIndexAttribute()
    {
        $user = request()->user('sanctum');
        if (!$user) {
            return null;
        } else {
            $users = $this->users;
            $user_reading = $users->where('id', $user->id)->first();


            return $user_reading ? $user_reading->pivot->index : null;
        }
    }
    public function getNewestChapterAttribute()
    {
        return Chapter::query()->whereStoryId($this->id)->latest()->first(['name', 'index']);
    }
    public function getTruncateDescriptionAttribute()
    {
        return Str::limit($this->description);
    }
    public function author()
    {
        return $this->belongsTo(Author::class);
    }
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
    public function converter(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id", "converter");
    }
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(["index", "notified",]);
    }
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, "commentable");
    }
    public function reports()
    {
        return $this->morphMany(Report::class, "reportable");
    }
}
