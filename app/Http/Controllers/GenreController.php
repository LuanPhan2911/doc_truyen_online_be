<?php

namespace App\Http\Controllers;

use App\Enums\GenreType;
use App\Http\Requests\GetGenreRequest;
use App\Models\Genre;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Traits\ResponseTrait;

class GenreController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GetGenreRequest $request)
    {
        $arr = [];
        $query = Genre::query()->select(['name', 'id', 'type', 'slug']);
        if ($request->has('type')) {

            $type = intval($request->type);
            $query->where('type', $type);
            $arr = $query->get();
        } else {
            $arr = $query->get();
        }

        return $this->success([
            'data' => $arr
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreGenreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGenreRequest $request)
    {
        $arr = $request->all();
        $data = [];
        foreach ($arr as $each) {
            $genre = Genre::create($each);
            $data[] = $genre;
        }
        return $this->success([
            "data" => $data
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Genre  $genre
     * @return \Illuminate\Http\Response
     */
    public function show(Genre $genre)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Genre  $genre
     * @return \Illuminate\Http\Response
     */
    public function edit(Genre $genre)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateGenreRequest  $request
     * @param  \App\Models\Genre  $genre
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $name = $request->get("name");
        $genre->name = $name;

        $genre->save();
        return $this->success([
            "data" => $genre
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Genre  $genre
     * @return \Illuminate\Http\Response
     */
    public function destroy(Genre $genre)
    {
        $genre->delete();
        return $this->success();
    }
}
