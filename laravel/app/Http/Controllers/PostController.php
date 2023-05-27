<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $posts = Post::with(['comments', 'comments.replies' => function ($query) {
            $query->with('nestedReplies');
        }])->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts,
        ]);
    }
    public function userPosts()
    {
        $user = Auth::user();
        $posts = Post::with('comments')->where('user_id',$user->id)->get();
        return response()->json([
            'status' => 'success',
            'posts' => $posts,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);
        $user = Auth::user();
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' =>$user->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully',
            'post' => $post,
        ]);
    }

    public function show($id)
    {
        $post = Post::find($id);
        return response()->json([
            'status' => 'success',
            'post' => $post,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $post = Post::find($id);
        $post->title = $request->title;
        $post->body = $request->body;
        $post->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Post updated successfully',
            'post' => $post,
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $post = Post::find($id);
        if(!$post){
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found',
                ]);
        }
        if($post->user_id !== $user->id){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to delete this post',
                ]);
        }
        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully',
            'post' => $post,
        ]);
    }
}
