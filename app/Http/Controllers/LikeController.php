<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\Like;

use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function getLikes($id){
        $post = Like::where('post_id',$id)->with('user')->get(); // get  all 
        // get only users from likes
        $users = $post->pluck('user');
        // count likes forech post
        // foreach($post as $p){
        //     $p['likes_count'] = $p->likes()->count();
        //     $p['comments_count'] = $p->comments()->count();
        //     $p['liked'] = $p->likes()->contain(auth()->user()->id);
        // }
        return response()->json([
            'status' => 'sucess',
            'data' => $users
        ],200);
    }

    public function toggleLike($id){
        $user = auth()->user(); // get current user logged in
        $post = Post::find($id);  // find post in database
        $liked = $post->likes->contains($user->id);
        if($liked){
            // remove like by user_id and post_id
            $like = Like::where('user_id',$user->id)->where('post_id',$post->id)->first();
            // alread like after delete
            return response()->json([
                'status' => 'success'
            ],200);
        }else{
            // create like by user_id and post_id
            $like = Like::create([
                'user_id' => $user->id,
                'post_id' => $post->id
            ]);
            return response()->json([
                'status' => 'success',
                'data' => $like
            ],200);
        }

    }
}
