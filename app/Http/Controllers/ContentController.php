<?php

namespace App\Http\Controllers;

use App\Http\Requests\Content\IndexContentRequest;
use App\Http\Requests\Content\StoreContentRequest;
use App\Http\Resources\ContentResource;
use App\Models\Content;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller
{
    use ApiResponse;

    public function index(IndexContentRequest $request): JsonResponse
    {
        $contents = Content::query()
            ->with('user')
            ->latest()
            ->paginate((int) $request->input('per_page', 10));

        return $this->successResponse(
            ContentResource::collection($contents->getCollection())->resolve(),
            'Contents retrieved successfully',
            200,
            [
                'current_page' => $contents->currentPage(),
                'last_page' => $contents->lastPage(),
                'per_page' => $contents->perPage(),
                'total' => $contents->total(),
            ]
        );
    }

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

    public function show(int $id): JsonResponse
    {
        $content = Content::with('user')->find($id);

        if (! $content) {
            return $this->errorResponse('Content not found', null, 404);
        }

        return $this->successResponse(
            new ContentResource($content),
            'Content retrieved successfully'
        );
    }
}
