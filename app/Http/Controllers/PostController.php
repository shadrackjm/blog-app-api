<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(){
        return response([
            'posts' => Post::orderBy('created_at','desc')->with('user:id,name,image')->withCount('comments','likes')
            ->with('likes', function($like){
                return $like->where('user_id', auth()->user()->id)
                    ->select('id','user_id','post_id')->get();
            })
            ->get()
        ],200);
    }

    public function show($id){
        return response([
            'post' => Post::where('id',$id)->withCount('comments','likes')->get()
        ]);
    }

    public function store(Request $request){
        // validate
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);
        
        try {
            // save image
           
         $image = $this->saveImage($request->image, 'posts');
            $post = Post::create([
                'body' => $attrs['body'],
                'user_id' => auth()->user()->id,
                'image' => $image,
            ]);
    
            return response([
                'message' => 'Post Created',
                'post' => $post
            ],200);

        } catch (\Exception $e) {
            return response([
                'message' => $e->getMessage(),
            ],403);
        }
        
    }

    // update
    public function update(Request $request, $id){

        $post = Post::find($id);

        if (!$post) {
           return response([
                'message' => 'Post not Found'
           ],403);
        }
        if ($post->user_id != auth()->user()->id) {
            return response([
                 'message' => 'Permission Denied.'
            ],403);
         }
        // validate
        $attrs = $request->validate([
            'body' => 'required|string'
        ]); 

       $post->update([
            'body' => $attrs['body']
       ]);
        // skip post image

        return response([
            'message' => 'Post Updated',
            'post' => $post
        ],200);
    }

    // delete
    public function destroy($id){
        try {
             $post = Post::find($id);

        if (!$post) {
           return response([
                'message' => 'Post not Found'
           ],403);
        }
        if ($post->user_id != auth()->user()->id) {
            return response([
                 'message' => 'Permission Denied.'
            ],403);
         }

         $post->comments()->delete();
         $post->likes()->delete();
         $post->delete();

         return response([
            'message' => 'Post Deleted',
        ],200);
        } catch (\Exception $e) {
            return response([
                'message' => $e->getMessage()
           ],403);
        }
        
    }
}
