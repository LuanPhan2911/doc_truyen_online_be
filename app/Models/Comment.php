<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    use \Conner\Likeable\Likeable;
    public $fillable = ["user_id", "message", "parent_id"];
    public function commentable()
    {
        return $this->morphTo();
    }
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reports()
    {
        return $this->morphMany(Report::class, "reportable");
    }
}
