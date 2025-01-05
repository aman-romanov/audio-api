<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
     * @OA\Schema(
     *     schema="Tag",
     *     type="object",
     *     description="Тэги и посты",
     *     required={"id", "name", "posts"},
     *     @OA\Property(
     *         property="title", 
     *         type="string",
     *         example="Заголовок",
     *         description="Загаловок тэга"),
     *     @OA\Property(
     *         property="posts", 
     *         type="array",
     *         @OA\Items(
     *             type="object",
     *             @OA\Property(
     *                 property="title", 
     *                 type="string",
     *                 example="Заголовок",
     *                 description="Загаловок поста"),
     *             @OA\Property(
     *                 property="description", 
     *                 type="text",
     *                 example="Описание поста с аудио-файлом",
     *                 description="Описание поста"),
     *             @OA\Property(
     *                 property="user_id", 
     *                 type="integer",
     *                 example=4,
     *                 description="ID автора поста"),
     *             @OA\Property(
     *                 property="is_active", 
     *                 type="boolean",
     *                 example=1,
     *                 description="Статус поста"),
     *             @OA\Property(
     *                 property="file_path", 
 *                     type="string",
     *                 example="compressed/audio_name.mp3"),
     *          ),
     *         description="Посты с этим тэгом"),
     * )
     */
    class TagSchema
    {
    
    }