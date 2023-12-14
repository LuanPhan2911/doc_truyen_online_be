<?php

namespace App\Models;

use DevDojo\LaravelReactions\Contracts\ReactableInterface;
use DevDojo\LaravelReactions\Traits\Reactable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model  implements ReactableInterface
{
    use Reactable;
    use HasFactory;
    protected $fillable = ['name', 'story_id', 'content', 'index'];
    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
