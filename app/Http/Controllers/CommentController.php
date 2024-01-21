<?php

namespace App\Http\Controllers;

use App\Enums\TypeCommentEnum;
use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateCommentRequest;

use App\Models\Story;
use App\Traits\ResponseTrait;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

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
                "user:id,name,avatar",
                "likeCounter:id,likeable_id,count",
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
    public function getRelies(Request $request)
    {
        $comment_id = $request->get("comment_id");
        $comments = Comment::query()
            ->with([
                "user:id,name,avatar",
                "likeCounter:id,likeable_id,count",
            ])
            ->whereParentId($comment_id)
            ->cursorPaginate(10);

        return $this->success([
            'data' => $comments
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
        $user = $request->user('sanctum');
        $arr = $request->only([
            "message",
            'is_leak',
            'type'
        ]);
        $arr['user_id'] = $user->id;
        $commentedId = $request->get("commentable_id");
        $commentable_type = $request->get("commentable_type");
        $parent_id = $request->get("parent_id");

        $comment = new Comment($arr);
        $comment->parent_id = $parent_id;


        $morphClass = null;
        switch ($commentable_type) {
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
            $comment->loadCount('replies');
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
    public function like(Request $request, Comment $comment)
    {
        $user = $request->user('sanctum');
        if ($comment->liked($user->id)) {
            $comment->unlike($user->id);
            return $this->success([
                "data" => [
                    'action' => 'unlike'
                ]
            ]);
        }
        $comment->like();
        return $this->success([
            "data" => [
                'action' => 'like'
            ]
        ]);
    }
}
