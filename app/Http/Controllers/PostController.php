<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('posts.post');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id = null)
    {
        // You can use the $id variable here
        return view('posts.create', compact('id'));
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);
  
        $imageUrl = null;

        if ($request->hasFile('imgUpload1')) {
            $imagePath = $request->file('imgUpload1')->store('images', 'public');
            $imageUrl = Storage::url($imagePath);
        } else {
            return response()->json(['error' => 'Image upload failed!'], 400);
        }
        $thumbnaildo = 'true';
        $post = new Post();
        $post->user_id = auth()->id(); // Assuming user is authenticated
        $post->title = $request->title;
        $post->thumbnail = $thumbnaildo;
        $post->image =  $imageUrl;
        $post->content = $request->content;
        $post->save();

        return response()->json(['success' => 'Post created successfully!', 'image_url' => Storage::url($post->image)]);
    }

    /**
     * Display the specified resource.
     */
  
        public function show()
        {
            $posts = Post::all();
            return response()->json($posts);
        }
    

        public function getByUserId()
        {
         
            $posts = Post::where('user_id', auth()->id())->get();
            return response()->json($posts);
        }
        public function getPostId($id)
        {
            // Fetch the post with the related user
            $post = Post::with('user')->findOrFail($id);
        
            // Return the post data with selected user information included
            return response()->json([
                'id' => $post->id,
                'user_id' => $post->user_id,
                'title' => $post->title,
                'thumbnail' => $post->thumbnail,
                'image' => $post->image,
                'content' => $post->content,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'user' => [
                    'id' => $post->user->id,
                    'username' => $post->user->username, // Adjust fields as necessary
                    'email' => $post->user->email
                ]
            ]);
        }
    
        public function destroy($id)
        {
            $post = Post::find($id);
            if ($post) {
                $post->delete();
                return response()->json(['success' => 'Post deleted successfully!']);
            } else {
                return response()->json(['error' => 'Post not found!'], 404);
            }
        }

        public function update(Request $request, $id)
        {
         
        
      
        
            $request->validate([
                'title' => 'required',
                'content' => 'required',
            ]);
      
            $imageUrl = null;
    
            if ($request->hasFile('imgUpload1')) {
                $imagePath = $request->file('imgUpload1')->store('images', 'public');
                $imageUrl = Storage::url($imagePath);
            } else {
                return response()->json(['error' => 'Image upload failed!'], 400);
            }
            $thumbnaildo = 'true';
            $post = new Post();
            $post->user_id = auth()->id(); // Assuming user is authenticated
            $post->title = $request->title;
            $post->thumbnail = $thumbnaildo;
            $post->image =  $imageUrl;
            $post->content = $request->content;
            $post->save();
    
            return response()->json(['success' => 'Post created successfully!', 'image_url' => Storage::url($post->image)]);
        }
        
        public function getPostsByTitle(Request $request)
        {
           
            $request->validate([
                'title' => 'required|string'
            ]);
          
            $title = $request->input('title');
            $posts = Post::where('title', 'like', '%' . $title . '%')->get();
    
            return response()->json($posts);
        }
}
