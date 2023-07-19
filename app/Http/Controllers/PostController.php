<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class PostController extends Controller
{
    //save post to database
    public function store(Request $request){
        $user = auth()->user();  // get current user logged in
        $data = $request->all(); // catching data that user thorw
        $data['user_id'] = $user->id; // add user id to database
        // check before login database should look column photo
        if($request->hasFile('photo')){
                $image = $request->file('photo');
                $name  = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/posts');
                $image->move($destinationPath, $name);
                $data['photo'] = $name;
            
        }
        $post = Post::create($data); // save post to database
        return response()->json([
            'status' => 'success',
            'data' => $post,
        ],200);
       // dd($user);  // run debug
    //    $data = $request->validate([
    //         'title' => 'required'
    //    ]);
    }  
    // get all in functuion store
    public function index(){
        // $post = Post::with('user')->get();
        $posts = Post::with('user')->latest()->paginate(10);  // page can get how much
       /// count number of likes foreach post
        foreach($posts as $post){
        // count like foreach post
           $post['likes_count'] = $post->likes->count();
        // comment count 
           $post['comment_count'] = $post->comments->count();
        // check you like or not
            $post['like'] = $post->likes->contains(auth()->user()->id); 
        }
        return response()->json([
            'status' => 'success',
            'date' => $posts
        ],200);
   }
   // get detail of a post
   public function show($id){
        $post = Post::with(['user','comments.user','likes'])->find($id);
        $post['likes_count'] = $post->likes->count();
        $post['comments_count'] = $post->comments->count();
        $post['liked'] = $post->likes->contains(auth()->user()->id);
        return response()->json([
            'status' => 'success',
            'data' => $post
        ],200);
   }

   // update post
   public function update(Request $request,$id){
      $post = Post::find($id); // find post by id
      // if post not fount
      if(!$post){
        return response()->json([
            'status' => 'error',
            'message' => 'post not found'
        ],404);
      }
      // if post found check if user is authorized to update post
      if(auth()->user()->id != $post->user_id){
        return response()->json([
            'status' => 'error',
            'message' => 'you are not athorized to update this post'
        ],401);
      } 
      $data = $request->all();
      // check if request has photo
      if($request->hasFile('photo')){
            $image = $request->file('photo');
            $name  = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/profile');
            $image->move($destinationPath, $name);
            //$post->photo = $name;
            $data['photo'] = $name;

            //get old path photo
            if($post->phto){
                $oldImage = public_path('/posts/').$post->photo;
                if(file_exists($oldImage)){
                    unlink($oldImage);
                }
            }
       }

       // check if request has tit
        $post->update($data);
        return response()->json([
            'status' => 'success',
            'data' => $post
        ],200); // update post
    }

   // delete post
   public function destory($id){
    $post = Post::find($id); //  find post by id
        if(!$post){
           return response()->json([
                'status' => 'error',
                'message' => 'post not found'
            ],404);
        }
    // if post found check if user is authorized to update post
       if(auth()->user()->id != $post->user_id){
            return response()->json([
              'status' => 'error',
              'message' => 'you are not athorized to update this post'
            ],404);
        }

        // delete post
        $post->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'post deleted successfully'
        ],200);
   }
   
}
    