<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    // like or dislike
    public function likeOrUnlike($id){
        $post = Post::find($id);

        if (!$post) {
            return response([
                 'message' => 'Post not Found'
            ],403);
         }

         $like = $post->likes()->where('user_id',auth()->user()->id)->first();
         
         if (!$like) {
            Like::create([
                'post_id' => $id,
                'user_id' => auth()->user()->id
            ]);
            return response([
                'message' => 'Liked'
           ],200);
         }
        //  dislike if not
        $like->delete();
        return response([
            'message' => 'Disliked'
       ],200);
    }
}
