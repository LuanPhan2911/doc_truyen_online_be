<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;

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
                    "user:id,name,avatar"
                ],
                "user:id,name,avatar",
                "likeCounter:id,likeable_id,count"
            ])
            ->withCount("replies")
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
            ->cursorPaginate(10);


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
        $commentedId = $request->get("commentedId");
        $type = $request->get("type");
        $parent_id = $request->input("parent_id");

        $comment = new Comment($arr);
        $comment->parent_id = $parent_id;
        $morphClass = null;
        switch ($type) {
            case 'story':
                $morphClass = Story::find($commentedId);
                # code...
                break;

            default:
                # code...
                break;
        }
        if (!empty($morphClass)) {
            $morphClass->comments()->save($comment);
            $comment->load([
                "user:id,name,avatar",
                "likeCounter:id,likeable_id,count"
            ]);
            if (!$comment->parent_id) {
                $comment->load([
                    "replies" => [
                        "user:id,name,avatar"
                    ],
                ]);
            }
            return $this->success([
                "data" => $comment,
            ]);
        }

        return $this->failure([
            "message" => "MorphClass not found"
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
        if (!empty($comment)) {
            $comment->replies()->delete();
            $comment->reports()->delete();
            $comment->delete();
            return $this->success([
                "message" => "Deleted"
            ]);
        }
        return $this->failure([
            "message" => "Delete fail"
        ]);
    }
    public function like(Comment $comment)
    {
        $userId = request()->user()->id;
        if ($comment->liked($userId)) {
            $comment->unlike($userId);
            return $this->success([
                "data" => "unlike"
            ]);
        }
        $comment->like();
        return $this->success([
            "data" => "like"
        ]);
    }
}
