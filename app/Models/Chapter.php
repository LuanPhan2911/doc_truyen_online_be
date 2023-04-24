<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'story_id', 'content', 'index'];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
