<?php

namespace App\Http\Controllers;

use App\Enums\GenreType;
use App\Http\Requests\StoreGenreRequest;
use App\Models\Genre;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test()
    {
        dd(GenreType::getValues());
    }
}
