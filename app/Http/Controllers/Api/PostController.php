<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Services\AudioService;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Info(title="Audio API", version="1.0.0"),
 * @OA\PathItem(path="/api")
 */
class PostController extends Controller
{
    protected $postService;
    protected $audioService;

    public function __construct(PostService $postService, AudioService $audioService)
    {
        $this->postService = $postService;
        $this->audioService = $audioService;
    }

    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Вывод список постов",
     *     description="Список постов с пагинацией",
     *     tags={"Post"},
     *     @OA\Response(
     *         response=200,
     *         description="Список",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PostResponse")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index()
    {
        Gate::authorize('viewAny', Post::class);
        $posts = $this->postService->showPosts();
        return PostResource::collection($posts->with('tags')->paginate(10));
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Создание нового поста",
     *     description="Новый пост",
     *     tags={"Post"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PostRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Вывод поста",
     *         @OA\JsonContent(ref="#/components/schemas/PostResponse")
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function store(PostRequest $request)
    {
        $user = auth('sanctum')->user();
        Gate::authorize('create', Post::class);

        $data = $request->validated();
        $post = $this->postService->createPost($data, $user->id);
        $audio_path = $this->audioService->processAudio($request->file, $post->id);
        $post->audio_path = $audio_path;

        return new PostResource($post->load('tags'));
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{post}",
     *     summary="Вывод одного поста",
     *     description="Новый пост",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", description="ID поста", example="5")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Вывод поста",
     *         @OA\JsonContent(ref="#/components/schemas/PostResponse")
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function show($id)
    {
        $post = $this->postService->showPost($id);
        return new PostResource($post->load('tags'));

    }

    /**
     * @OA\Put(
     *     path="/api/posts/{post}",
     *     summary="Редактура одного поста",
     *     description="Изменение существующего поста по ID",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", description="ID поста", example="5")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PostRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Вывод поста",
     *         @OA\JsonContent(ref="#/components/schemas/PostResponse")
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function update(PostRequest $request, $id)
    {   
        $user = auth('sanctum')->user();
        $post = $this->postService->showPost($id);

        Gate::authorize('update', $post);

        $data = $request->validated();
        $post = $this->postService->updatePost($id, $data, $user->id);
        if (isset($request->file)) {
            $audioPath = $this->audioService->processAudio($request->file, $post->id);
            $post->audio_path = $audioPath;
        }

        return new PostResource($post->load('tags'));
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Удаление поста",
     *     description="Удаление поста по ID",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Post ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Пост удален"
     *     ),
     *     @OA\Response(response=404, description="Post not found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function destroy(string $id)
    {
        $user = auth('sanctum')->user();
        $post = $this->postService->showPost($id);

        Gate::authorize('delete', $post);

        Storage::delete($post->audio_path);
        $this->postService->deletePost($id, $user->id);

        return response()->json(['message' => 'Пост удален'],204);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{user}/posts",
     *     summary="Вывод постов пользователя по ID",
     *     description="Вывод постов",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", description="ID пользователя", example="5")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PostResponse")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function userPosts($user_id)
    {
        $posts = $this->postService->showUserPosts($user_id);

        return PostResource::collection($posts->load('tags'));
    }
}
