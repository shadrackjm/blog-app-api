<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($id){
        $post = Post::find($id);

        if (!$post) {
           return response([
                'message' => 'Post not Found'
           ],403);
        }
        return response([
            'comments' => $post->comments()->with('user:id,name,image')->get()
        ],200);
    }

    public function store(Request $request, $id){
        $post = Post::find($id);

        if (!$post) {
           return response([
                'message' => 'Post not Found'
           ],403);
        }
         // validate
         $attrs = $request->validate([
            'comment' => 'required|string'
        ]); 

        Comment::create([
            'comment' => $attrs['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id
        ]);

        return response([
            'message' => 'Comment created',
        ],200);
    }

    // update
    public function update(Request $request, $id){
        $post = Comment::find($id);

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

         $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $attrs['comment'],
        ]);

        return response([
            'message' => 'Comment updated.'
       ],403);

    }

    public function destroy($id){
        $post = Comment::find($id);

        if (!$post) {
           return response([
                'message' => 'Comment not Found'
           ],403);
        }
        if ($post->user_id != auth()->user()->id) {
            return response([
                 'message' => 'Permission Denied.'
            ],403);
         }

        $comment->delete();

        return response([
            'message' => 'Comment deleted.'
       ],403);

    }
}
