<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateStory extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ['comment_id'];
    protected $casts = [
        'characteristic' => 'float',
        'plot' => 'float',
        'world_building' => 'float',
        'quality_convert' => 'float',
    ];
}
