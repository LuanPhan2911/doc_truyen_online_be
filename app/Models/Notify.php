<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notify extends Model
{
    use HasFactory;
    protected $fillable = ["user_id", "index"];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
