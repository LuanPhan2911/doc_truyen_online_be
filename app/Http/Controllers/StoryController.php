<?php

namespace App\Http\Controllers;

use App\Enums\GenreType;
use App\Models\Story;
use App\Http\Requests\StoreStoryRequest;
use App\Http\Requests\UpdateStoryRequest;
use App\Models\Chapter;
use App\Models\Comment;
use App\Traits\ResponseTrait;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $stories = [];
        $query = Story::query()
            ->with([
                "genres:id,name,type",
            ])
            ->withCount("chapters");
        if ($request->has('orderBy')) {
            $orderBy = $request->get("orderBy");
            $query->orderBy("updated_at", $orderBy);
        } else {
            $query->orderBy("updated_at", "desc");
        }


        if ($request->has("genres_id")) {

            $genres_id = $request->get("genres_id");
            $query->whereHas('genres', function ($q) use ($genres_id) {
                return $q->whereIn('genre_id', $genres_id);
            });
        }
        if ($request->has("name")) {
            $name = $request->name;
            $query->where("name", "like", "%" . $name . "%");
        }
        if ($request->has("filter")) {
            $stories =  $query->paginate(10);
        } else {
            $stories = $query->limit(8)->get();
        }

        $stories->makeHidden('description');
        $stories->append(['truncate_description']);
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
            "author_name",
        ]);
        $genres_id = $request->safe()->genres_id;
        if ($request->hasFile('avatar')) {
            $path = Storage::disk("public")->put('stories', $request->file('avatar'));
            // Cloudinary::uploadApi();
            // $uploadedAvatar = $request->file('avatar')->storeOnCloudinary('stop_truyen');
            // $arr["avatar"] = $uploadedAvatar->getPublicId();
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
            "genres:id,name,type,slug",


        ])
            ->loadCount("chapters");

        $story->append(
            [
                'comments_count',
                'reaction_summary',
                'newest_chapter',
                'rate_comments_count',
                'rate_story',
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
            "author_name"
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
        $story->slug = SlugService::createSlug(Story::class, 'slug', $story->name);
        $story->save();
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
        $stories = Story::query()
            ->with([
                "genres:id,name,type"
            ])
            ->whereUserId($userId)
            ->get();
        return $this->success([
            "data" => $stories
        ]);
    }
    public function adminShow(Story $story)
    {
        $story->load([
            "converter:id,name",
            "genres:id,name,type",

        ]);
        return $this->success([
            'data' => $story
        ]);
    }
}
