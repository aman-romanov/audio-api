<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tags",
     *     summary="Вывод списка тэгов с постами",
     *     description="Список тэгов с пагинацией",
     *     tags={"Tag"},
     *     @OA\Response(
     *         response=200,
     *         description="Список",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Tag")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index()
    {
        $tags = Tag::all()->load('posts');
        return TagResource::collection($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required | string'
        ]);

        $tag = Tag::create([
            'name' => $data['name']
        ]);

        return new TagResource($tag);
    }

    /**
     * @OA\Get(
     *     path="/api/tags/{tag}",
     *     summary="Вывод одного тэга с постами",
     *     tags={"Tag"},
     *     @OA\Parameter(
     *         name="tag",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", description="ID тэга", example="5")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Вывод поста",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function show(string $id)
    {
        $tag = Tag::findOrFail($id)->load('posts');
        return new TagResource($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required | string'
        ]);

        $tag = Tag::findOrFail($id);

        $tag->update([
            'name' => $data['name']
        ]);

        return new TagResource($tag);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tag = Tag::findOrFail($id);

        $tag->delete();

        return response()->json(['message' => 'Тэг удален'], 204);
    }
}
