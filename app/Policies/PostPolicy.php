<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    public function viewUser(User $user, Post $posts): Response
    {
        return $user->id === $posts->first()->user_id
                ? Response::allow()
                : Response::deny('Это не ваш аккаунт');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): Response
    {
        return $user->id === $post->user_id
                ? Response::allow()
                : Response::deny('Только автор поста может редактировать');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): Response
    {
        return $user->id === $post->user_id
                ? Response::allow()
                : Response::deny('Только автор поста может удалять');
    }
}
