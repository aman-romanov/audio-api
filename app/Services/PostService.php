<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;

class PostService
{

    public function showPosts(){
        $posts = Post::with('tags');
        return $posts;
    }

    public function showPost($id){
        $post = Post::findOrFail($id);
        return $post;
    }

    public function showUserPosts($user_id){
        $posts = Post::where('user_id', $user_id)->get();
        return $posts;
    }

    public function createPost($data, $userId)
    {
        $post = Post::create([
            'user_id' => $userId,
            'title' => $data['title'],
            'description' => $data['description'],
            'is_active' => $data['is_active']
        ]);

        if (isset($data['tags'])) {
            $tags = Tag::find($data['tags']);
            $post->tags()->sync($tags);
        }

        return $post;
    }

    public function updatePost($postId, $data, $userId)
    {
        $post = Post::findOrFail($postId);

        $post->title = $data['title'];
        $post->description = $data['description'];
        $post->save();

        if (isset($data['tags'])) {
            $tags = Tag::find($data['tags']);
            $post->tags()->sync($tags);
        }

        return $post;
    }

    public function deletePost($postId, $userId)
    {
        $post = Post::findOrFail($postId);
        
        $post->delete();
    }
}