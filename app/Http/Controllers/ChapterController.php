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
    public function store(StoreChapterRequest $request, $storyId)
    {
        $index = 1;

        $maxChapterIndex = Chapter::query()->where("story_id", $storyId)->max("index");
        if (!empty($maxChapterIndex)) {
            $index = $maxChapterIndex + 1;
        }
        $arr = $request->only([
            "name",
            "content"
        ]);
        $arr["index"] = $index;
        $arr["story_id"] = $storyId;
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
    public function show(Story $story, $chapterIndex)
    {


        $storyId = $story->id;
        $chapter = Chapter::query()
            ->with("story:id,author_name,name")
            ->where([
                ["story_id", $storyId],
                ["index", $chapterIndex]

            ])->first();
        $countChapter = Chapter::query()->where("story_id", $storyId)->count();
        return $this->success([
            'data' => [
                "chapter" => $chapter,
                "count" => $countChapter,
                "storyId" => $storyId
            ]


        ]);
    }
    public function adminShow($storyId, $chapterIndex)
    {



        $chapter = Chapter::query()
            ->with("story:id,author_name,name")
            ->where([
                ["story_id", $storyId],
                ["index", $chapterIndex]

            ])->first();

        return $this->success([
            'data' => $chapter




        ]);
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
    public function update(UpdateChapterRequest $request, $chapterId)
    {

        $chapter = Chapter::query()
            ->find($chapterId);
        $arr = $request->only([
            "name",
            "content",
        ]);
        $chapter->update($arr);
        return $this->success([
            "data" => $chapter,
        ]);
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
