<?php

namespace Database\Seeders;

use App\Models\Tag;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(25)->create()->each(function($user){
            $postNum = random_int(1, 5);
            Post::factory($postNum)->create([
                'user_id' => $user->id
            ]);
        });

        Tag::factory(15)->create();

        $posts = Post::all();
        foreach($posts as $post){
            $tags = Tag::inRandomOrder()->take(rand(2,5))->pluck('id');
            $post->tags()->attach($tags);
        }
    }
}
