<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Chapter;
use App\Models\Story;
use App\Traits\ResponseTrait;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $story_id = $request->get("story_id");
        $comments = Comment::query()
            ->with([
                "replies" => [
                    "user"
                ],
                "user"
            ])
            ->whereHasMorph(
                'commentable',
                [Story::class],
                function (Builder $query) use ($story_id) {
                    $query->where('id', $story_id);
                }
            )
            ->where([
                'parent_id' => null
            ])
            ->latest()
            ->paginate(5);
        return $this->success([
            "data" => $comments,
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
     * @param  \App\Http\Requests\StoreCommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommentRequest $request)
    {
        $arr = $request->only([
            "message",
            "user_id"
        ]);
        $parent_id = $request->input("parent_id");
        $story_id = $request->input("story_id");
        $comment = new Comment($arr);
        $comment->parent_id = $parent_id;
        $story = Story::query()->find($story_id);
        $story->comments()->save($comment);
        return $this->success([
            "data" => $comment
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCommentRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
