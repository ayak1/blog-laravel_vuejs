<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index($post_id)
    {
        $post = Post::where('id',$post_id)->first();
        if(!$post){
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found.'
                ]);
        }
        $comments = $post->comments;
        if(!$comments){
            return response()->json([
                'status' => 'error',
                'message' => 'No comments found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'comments' => $comments
        ]);
    }

    public function store(Request $request,$post_id)
    {
        $validatedData = $request->validate([
            'comment' => 'required|string',
        ]);
        $user = Auth::user();
        if(!$user){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not logged in.'
                ]);
        }
        $post = Post::where('id',$post_id)->first();
        if(!$post){
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found.'
                ]);
        }
        $comment = new Comment;
        $comment->comment = $validatedData['comment'];
        $comment->user_id = auth()->id();
        $post->comments()->save($comment);


        return response()->json([
            'status' => 'success',
            'message' => 'Comment created successfully',
            'comment' => $comment,
        ],201);
    }
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = Comment::find($id);
        if(!$comment){
            return response()->json([
                'status' => 'error',
                'message' => 'Comment not found.'
                ]);
        }
        if($comment->user_id !== $user->id){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to edit this comment.'
                ]);
        }
        $comment->comment = $request->comment;
        $comment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment updated successfully',
            'comment' => $comment,
        ]);
    }
    public function destroy($id)
    {
        $user = Auth::user();
        $comment = Comment::find($id);
        if(!$comment){
            return response()->json([
                'status' => 'error',
                'message' => 'Comment not found',
                ]);
        }
        if($comment->user_id !== $user->id){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to delete this comment',
                ]);
        }
        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment deleted successfully',
            'comment' => $comment,
        ]);
    }
}
