<?php

namespace App\Http\Controllers;

use App\Enums\ViewStoryEnum;
use App\Models\Story;
use App\Http\Requests\StoreStoryRequest;
use App\Http\Requests\UpdateStoryRequest;
use App\Traits\ResponseTrait;
use Cviebrock\EloquentSluggable\Services\SlugService;
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
    public function getFilterStory(Request $request)
    {
        $query = Story::query()
            ->with([
                "genres:id,name,type",
                "author:id,name,slug"
            ])
            ->withCount("chapters");
        $sort_by = $request->get('sort_by') ?? "favorite";
        switch ($sort_by) {
            case 'newest_updated':

                $query->limit(10)
                    ->latest('updated_at');

                break;
            case 'newest_created':
                $query->limit(10)
                    ->latest('created_at');

                break;
            case 'favorite':
                $query->limit(6);

                break;

            default:
                $query->limit(10);

                break;
        }
        $stories = $query->get();
        $stories->makeHidden('description');
        $stories->append(['truncate_description', 'genre']);
        return $this->success(
            [
                'data' => $stories,
            ]
        );
    }
    public function index(Request $request)
    {

        $query = Story::with([
            "genres:id,name,type",
            "author:id,name,slug"
        ])
            ->withCount("chapters");

        $sort_by = $request->get('sort_by') ?? "favorite";
        switch ($sort_by) {
            case 'newest_updated':
                $query->latest('updated_at');

                break;
            case 'newest_created':
                $query->latest('created_at');
                break;
            case 'favorite':
                break;
            default:
                break;
        }

        if ($request->has("genres_id")) {
            $genres_id = explode(",", $request->get('genres_id'));
            $query->whereHas('genres', function ($q) use ($genres_id) {
                return $q->whereIn('genre_id', $genres_id);
            });
        }
        if ($request->has("name")) {
            $name = $request->name;
            $query->where("name", "like", "%" . $name . "%");
        }
        if ($request->has('view')) {
            $query->whereView($request->get('view'));
        }

        $stories = $query->paginate(10);

        $stories->makeHidden(['description']);
        $stories->append(['truncate_description', 'genre']);
        return $this->success([
            'data' => $stories
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
            'view',
            'user_id',
            'author_id'
        ]);
        $genres_id = $request->safe()->genres_id;
        if ($request->hasFile('avatar')) {
            $path = Storage::disk("public")->put('stories', $request->file('avatar'));
            $arr['avatar'] = $path;
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
    public function show(Request $request, Story $story)
    {
        $story->load([
            "converter:id,name",
            "genres:id,name,slug",
            "author:id,name,slug"


        ])
            ->loadCount("chapters");

        $story->append(
            [
                "genre",
                'comments_count',
                'reaction_summary',
                'newest_chapter',
                'chapter_index'
            ]
        );
        $story->makeHidden('users');
        return $this->success([
            "data" => $story,
        ]);
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
    public function update(UpdateStoryRequest $request)
    {
        $id = $request->get("id");
        $story = Story::query()
            ->with([
                "converter:id,name",
                "genres:id,name,type"
            ])
            ->find($id);
        $arr = $request->only([
            "name",
            "description",
            "status",
            "view",
            "author_id"
        ]);
        if ($request->has("genres_id")) {
            $genres_id = $request->get("genres_id");
            $story->genres()->sync($genres_id);
        }
        if ($request->hasFile("avatar")) {

            $path = Storage::disk("public")->put('stories', $request->file('avatar'));
            $arr['avatar'] = $path;
            if (!empty($story->avatar)) {
                Storage::disk("public")->delete($story->avatar);
            }
        }
        $story->update($arr);
        return $this->success([
            "data" => $story,
        ]);
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
    public function adminIndex(Request $request)
    {
        $userId = auth()->id();
        $stories = Story::with([
            "genres:id,name,type",
            "author:id,name,slug"
        ])
            ->whereUserId($userId)
            ->paginate(10);

        $stories->append([
            'genre',
            'truncate_description'
        ]);
        return $this->success([
            "data" => $stories
        ]);
    }
    public function adminShow(Story $story)
    {
        $story->load([
            "converter:id,name",
            "genres:id,name,type",
            "author:id,name"

        ]);
        return $this->success([
            'data' => $story
        ]);
    }
}
