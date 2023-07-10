<?php

namespace App\Http\Controllers;

use App\Enums\GenreType;
use App\Http\Requests\StoreGenreRequest;
use App\Models\Genre;
use App\Models\Story;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test()
    {
        $story = Story::query()->where('id', 16)->with('genres')->first();

        return view('test', ['avatar' => $story->avatar]);
    }
}
