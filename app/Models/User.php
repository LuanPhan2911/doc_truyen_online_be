<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use DevDojo\LaravelReactions\Traits\Reacts;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Reacts;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        "avatar",
        "description",
        "gender",
        "birth_date"

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'gender' => 'integer'
    ];
    public function reports()
    {
        return $this->morphMany(Report::class, "reportable");
    }
    public function stories()
    {
        return $this->belongsToMany(Story::class)
            ->as('story_user')
            ->withPivot([
                'index',
                'notified',
                'type'
            ])
            ->withTimestamps();
    }
    public function chapters()
    {
        return $this->belongsToMany(Chapter::class)
            ->withPivot('is_seen')
            ->withTimestamps();
    }
}
