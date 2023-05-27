<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUserPosts(){
        $user = Auth::user();
        if(!$user){
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
                ], 404);
        }
        $posts = Post::with('user')->where('user_id',$user->id)->all();
        if(!$posts){
            return response()->json([
                'status' => 'error',
                'message' => 'No posts found'
                ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Your all posts:',
            'posts' => $posts
        ]);
    }
}
