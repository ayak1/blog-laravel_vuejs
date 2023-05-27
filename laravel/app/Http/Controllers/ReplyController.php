<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{
    public function createReply(Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not logged in.'
                ], 401);
        }
        $request->validate(Reply::rules());
        $reply = new Reply();
        $reply->user_id = $user->id; // or however you get the authenticated user id
        $reply->post_id = $request->post_id;
        $reply->comment_id = $request->comment_id;
        $reply->parent_id =  $request->parent_id;
        $reply->reply =  $request->reply;
        $reply->save();
        
        // If the reply is a reply to a reply, update the parent's nested replies
        if ($reply->parent_id) {
            $parent = Reply::find($reply->parent_id);
            $parent->nestedReplies()->save($reply);
        }
        $reply->load('nestedReplies');

        return response()->json(['message' => 'Reply created successfully', 'reply' => $reply]);
    }
    public function getRepliesForComment($commentId)
    {
        return Reply::where('comment_id', $commentId)->get();
    }
    public function getRepliesForPost($postId)
    {
        return Reply::where('post_id', $postId)->get();
    }
    public function getRepliesForParent($parentId)
{
    return Reply::where('parent_id', $parentId)->get();
}
public function updateReply(Request $request, $id)
{
    $validatedData = $request->validate(Reply::rules());
    $reply = Reply::find($id);
    if (!$reply) {
        return response()->json(['message' => 'Reply not found'], 404);
    }
    $reply->comment_id = $validatedData['comment_id'];
    $reply->parent_id = $validatedData['parent_id'];
    $reply->reply = $validatedData['reply'];
    $reply->save();
    
    return response()->json(['message' => 'Reply updated successfully', 'reply' => $reply]);
}
}
