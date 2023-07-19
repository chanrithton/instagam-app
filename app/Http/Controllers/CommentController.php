<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\Comment;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    // get list comments for a post
    public function show($id){
        $post = Post::find($id);  // find post in database
        // if no post
        if(!$post){
            return response()->json([
                'status' => 'error',
                'message' => 'post not found'
            ],404);
        }
        $comments = $post->comments()->with('user')->get();  // get comment set by post
        return response()->json([ 
            'status' => 'sucess',
            'data' => $comments
        ],200);
    }
     // add comments to post
     public function store(Request $request,$id){
        $user = auth()->user(); // get current user logged in
        $post = Post::find($id);
        // if no post
        if(!$post){
            return response()->json([
                'status' => 'error',
                'message' => 'post not found'
            ],404);
        }
        $data = $request->all(); // get request data from user
        $data['user_id'] = $user->id; // add user_id to data
        //$comment = $post->comments()->create($data); // save comments into database
        $comments = $post->comments()->with('user')->get();
        // get only users  from like 
       // $users = $comments->pluck('user');
        return response()->json([
            'status' => 'success',
            'data' => $comments,
        ],200);
    }
    // update comment
    public function update(Request $request,$id){
        $user = auth()->user(); // get current user logged in
        $data = $request->all(); // get request data from user
        $data['user_id'] = $user->id; // add user_id to data
        $vaild = $this->validate($request,[
            'text' => 'required',
        ]);
        $comment = Comment::find($id);
        // if no comment
        if(!$comment){
            return response()->json([
                'status' => 'error',
                'message' => 'comment not fount'
            ],404);
        }
        // if user is not the owner of the comment
        if($comment->user_id != $user->id){
            return response()->json([
                'status' => 'error',
                'message' => 'you are not the owner of this comment'
            ],401);
        }
        $comment->update($vaild); // update comment  update($request->all());
        return response()->json([
            'status' => 'sucess',
            'data' => $comment
        ]);
    } 

    // delete comment
    public function destory($id){
        $user = auth()->user(); // get current user logged in
        $comment = Comment::find($id);
        // if no comment
        if(!$comment){
            return response()->json([
                'status' => 'error',
                'message' => 'comment not found'
            ],404);
        }
        // if user is not the owner of the comment
        if($comment->user_id != $user->id){
            return response()->json([
                'status' => 'error',
                'message' => 'you are not the owner of this comment'
            ],401);
        }
        $comment->delete(); // update comment  update($request->all());
        return response()->json([
            'status' => 'sucess',
            'message' => 'comment deleted'
        ]);

    }
}
