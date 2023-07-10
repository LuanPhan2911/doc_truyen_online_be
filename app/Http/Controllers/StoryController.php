<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Http\Requests\StoreStoryRequest;
use App\Http\Requests\UpdateStoryRequest;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stories = Story::query()->with([
            "user:id,name,avatar",
            "genres:name",

        ])->get();
        return $this->success([
            'data' => $stories,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreStoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStoryRequest $request)
    {
        $arr = $request->only([
            'name',
            'description',
            'status',
            'view',
            'user_id'
        ]);
        $genres_id = $request->safe()->genres_id;
        if ($request->hasFile('avatar')) {
            $path = Storage::disk("public")->put('stories', $request->file('avatar'));
            $arr["avatar"] = $path;
        }
        $story = Story::create($arr);
        $story->genres()->attach($genres_id);

        return $this->success([
            'data' => $story,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $name = $request->name;
        if (!empty($name)) {
            $story = Story::query()->where("slug", "=", $name)->first();
            return $this->success([
                'data' => $story
            ]);
            return $this->failure();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function edit(Story $story)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateStoryRequest  $request
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStoryRequest $request, Story $story)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function destroy(Story $story)
    {
        //
    }
}
