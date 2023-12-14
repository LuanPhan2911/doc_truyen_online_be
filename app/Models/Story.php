<?php

namespace App\Models;

use App\Enums\GenreType;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use Sluggable;
    use HasFactory;
    protected $with = [];
    public $guarded = [];
    protected $appends = ['genre'];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    public function getGenreAttribute()
    {
        return $this->genres->where('type', GenreType::CATEGORY)->first();
    }
    public function getCommentsCountAttribute()
    {
        return Comment::query()->whereHasMorph(
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
    public function getNewestChapterAttribute()
    {
        return Chapter::query()->whereStoryId($this->id)->latest()->first(['name', 'index']);
    }
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(["index", "notified", "marked"]);
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
