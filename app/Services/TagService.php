<?php 

namespace App\Services;

use App\Models\Tag;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TagService
{
    // CRUD операции для тегов

    // Создание нового тега
    public function createTag(string $name): Tag
    {
        return Tag::create([
            'name' => $name
        ]);
    }

    // Получение всех тегов
    public function getAllTags(): Collection
    {
        return Tag::all();
    }

    // Получение тега по ID
    public function getTagById(int $id): ?Tag
    {
        return Tag::find($id);
    }

    // Обновление существующего тега
    public function updateTag(int $id, string $name): ?Tag
    {
        $tag = Tag::find($id);
        if ($tag) {
            $tag->update(['name' => $name]);
        }
        return $tag;
    }

    // Удаление тега
    public function deleteTag(int $id): bool
    {
        $tag = Tag::find($id);
        if ($tag) {
            $tag->delete();
            return true;
        }
        return false;
    }

    // Фильтрация постов по тэгам
    public function filterPostsByTag(string $tagName): Collection
    {
        return Post::whereHas('tags', function ($query) use ($tagName) {
            $query->where('name', $tagName);
        })->get();
    }

    // Добавление тега к посту
    public function addTagToPost(int $postId, int $tagId): void
    {
        $post = Post::find($postId);
        if ($post) {
            $post->tags()->attach($tagId);
        }
    }

    // Удаление тега из поста
    public function removeTagFromPost(int $postId, int $tagId): void
    {
        $post = Post::find($postId);
        if ($post) {
            $post->tags()->detach($tagId);
        }
    }
}