<?php

namespace App\Http\Controllers\api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Auth::user()->id;
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
        try {
            $request->validate([
                'title' => 'required',
                'image' => 'required|file|mimes:png,jpg,jpeg',
                'content' => 'required',
            ]);

            $file = $request->file('image');

            $path = $file->store('post-images', 'public');

            $data = Post::create([
                'title' => $request->title,
                'image' => $path,
                'content' => $request->content,
                'user_id' => Auth::user()->id,
            ]);

            return response()->json([
                'message' => 'Post created successfully',
                'data' => $data
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Post creation failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Post $post)
    {
        $data = $post->find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Post found',
            'data' => $data
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
