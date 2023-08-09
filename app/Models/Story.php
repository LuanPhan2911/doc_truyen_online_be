<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    public $guarded = [];
    use Sluggable;
    use HasFactory;
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, "commentable");
    }
}
