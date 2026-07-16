<?php

namespace App\Http\Controllers;

use App\Http\Requests\Content\StoreContentRequest;
use App\Http\Resources\ContentResource;
use App\Models\Content;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller
{
    use ApiResponse;

    public function store(StoreContentRequest $request): JsonResponse
    {
        $content = Content::create([
            'user_id' => $request->user()->id,
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'image' => $request->validated('image'),
        ]);

        $content->load('user');

        return $this->successResponse(
            new ContentResource($content),
            'Content created successfully',
            201
        );
    }
}
