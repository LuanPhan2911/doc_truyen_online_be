<?php

namespace App\Http\Controllers;

use App\Enums\StoryUserType;
use App\Events\ChapterCreatedNotifies;
use App\Models\Chapter;
use App\Http\Requests\StoreChapterRequest;
use App\Http\Requests\UpdateChapterRequest;
use App\Models\Story;
use App\Models\User;
use App\Traits\ResponseTrait;
use DevDojo\LaravelReactions\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class ChapterController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Story $story)
    {
        $chapters = Chapter::query()
            ->select(["id", "name", "index", "created_at"])
            ->where('story_id', $story?->id)->get();

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
    public function store(StoreChapterRequest $request, Story $story)
    {
        $index = 1;

        $maxChapterIndex = Chapter::query()->where("story_id", $story?->id)->max("index");
        if (!empty($maxChapterIndex)) {
            $index = $maxChapterIndex + 1;
        }
        $arr = $request->only([
            "name",
            "content"
        ]);
        $arr["index"] = $index;
        $arr["story_id"] = $story?->id;
        $chapter = Chapter::create($arr);
        // ChapterCreatedNotifies::dispatch(
        //     $story
        // );
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
    public function adminShow(Story $story, $index)
    {

        $chapter = Chapter::query()
            ->where([
                ["story_id", $story->id],
                ["index", $index]

            ])->first();

        return $this->success([
            'data' => $chapter
        ]);
    }
    public function show(Request $request, Story $story, $index)
    {

        $chapter = Chapter::with([
            "story:id,name,author_id,slug" => [
                'author:id,name',
            ],
            // "reactions"
        ])

            ->where([
                ["story_id", $story->id],
                ["index", $index]

            ])->first();
        $chapter->story->append('comments_count');
        $chapter->story->loadCount('chapters');

        $user = $request->user('sanctum');
        if ($user) {
            // $user->chapters()->updateExistingPivot($chapter->id, [
            //     'is_seen' => 1
            // ]);

            $hasStory = $user->stories()
                ->wherePivot('type', StoryUserType::READING)
                ->where('stories.id', $story->id)->first();
            if ($hasStory) {
                $user->stories()
                    ->wherePivot('type', StoryUserType::READING)->updateExistingPivot(
                        $story->id,
                        [
                            "index" => $index,
                            "updated_at" => Date::now(),
                            "type" => StoryUserType::READING

                        ]
                    );
            } else {
                $user->stories()
                    ->wherePivot('type', StoryUserType::READING)->attach(
                        $story->id,
                        [
                            "index" => $index,
                            "created_at" => Date::now(),
                            "updated_at" => Date::now(),
                            "type" => StoryUserType::READING
                        ],

                    );
            }
        }



        // $userReaction = null;
        // $reactionSummary = $chapter?->getReactionsSummary();
        //     if (isset($reactionSummary) && $chapter->reacted($user)) {
        //         foreach ($chapter->reactions as $reaction) {
        //             $responder = $reaction->getResponder();
        //             if ($responder->id === $user->id) {
        //                 $userReaction = $reaction;
        //             }
        //         };
        //     }
        // }
        // $chapter['reaction'] = [
        //     'user' => $userReaction,
        //     'summary' => $reactionSummary
        // ];

        return $this->success([
            'data' => $chapter,
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
    public function update(UpdateChapterRequest $request, Story $story, $index)
    {

        $chapter = Chapter::whereStoryId($story->id)
            ->whereIndex($index)
            ->first();

        $arr = $request->validated();
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

    public function reaction(Request $request, Story $story, $chapterIndex)
    {
        $user = $request->user();
        if (empty($user)) {
            return $this->failure();
        }
        $reactionName = $request->get('name');
        $reaction = Reaction::query()->where('name', $reactionName)->first();

        $chapter = Chapter::query()->where([
            ['story_id', $story->id],
            ['index', $chapterIndex]
        ])->first();

        $user->reactTo($chapter, $reaction);
        $reactionSummary = $chapter->getReactionsSummary();
        return $this->success([
            'message' => "Thành công",
            "data" => [
                'summary' => $reactionSummary
            ]
        ]);
    }
}
