<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Http\Requests\StoreChapterRequest;
use App\Http\Requests\UpdateChapterRequest;
use App\Models\Story;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($storyId)
    {
        $chapters = Chapter::query()
            ->select(["id", "name", "index", "created_at"])
            ->where('story_id', $storyId)->get();

        return $this->success(
            [
                "data" => $chapters
            ]
        );
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
     * @param  \App\Http\Requests\StoreChapterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreChapterRequest $request)
    {
        $index = 1;
        $storyId = $request->get("story_id");
        $maxChapterIndex = Chapter::query()->max("index");
        if (!empty($maxChapterIndex)) {
            $index = $maxChapterIndex + 1;
        }
        $arr = $request->only([
            "name",
            "story_id",
            "content"
        ]);
        $arr["index"] = $index;
        $chapter = Chapter::create($arr);
        if (!empty($chapter)) {
            $story = Story::query()->find($storyId);
            $story->touch();
        }
        return $this->success([
            'data' => $chapter
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $storyId = $request->storyId;
        $chapterIndex = $request->chapterIndex;
        if (!empty($storyId) && !empty($chapterIndex)) {

            $chapter = Chapter::query()
                ->where([
                    ["index", $chapterIndex],
                    ["story_id", $storyId]
                ])
                ->with('story')
                ->first();
            if (!empty($chapter)) {
                return $this->success([
                    'data' => $chapter
                ]);
            }
        }
        return $this->failure([]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function edit(Chapter $chapter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateChapterRequest  $request
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateChapterRequest $request, Chapter $chapter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chapter $chapter)
    {
        //
    }
}
