<?php

namespace App\Http\Controllers;

use App\Models\comment;
use App\Models\Users;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      
        $request->validate([
        
            'content' => 'required',
        ]);
  

        $post = new comment();
        $post->user_id = auth()->id(); 
        $post->post_id = $request->post_id;
        $post->content = $request->content;
        $post->save();

        return response()->json(['success' => 'Post created successfully!']);
    }

    public function getCommentsByPostId($post_id)
    {
        // Validate that post_id is a valid integer
        if (!is_numeric($post_id)) {
            return response()->json(['error' => 'Invalid post ID'], 400);
        }
    
        // Fetch comments by post ID
        $comments = Comment::where('post_id', $post_id)->get();
    
        // Extract user IDs from comments
        $userIds = $comments->pluck('user_id')->unique();
    
        // Fetch users by the extracted user IDs
        $users = Users::whereIn('id', $userIds)->get()->keyBy('id');
    
        // Add user data to each comment
        $comments = $comments->map(function($comment) use ($users) {
            return [
                'id' => $comment->id,
                'post_id' => $comment->post_id,
                'content' => $comment->content,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'user' => [
                    'id' => $comment->user_id,
                    'username' => $users->get($comment->user_id)->username // Adjust field as necessary
                ]
            ];
        });
    
        // Return the comments as a JSON response
        return response()->json($comments);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(comment $comments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, comment $comments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(comment $comments)
    {
        //
    }
}
