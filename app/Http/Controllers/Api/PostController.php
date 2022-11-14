<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\PostServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpCodes;

class PostController extends BaseApiController
{
    private $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }
    public function obtenertodos(): JsonResponse
    {
        return $this->successResponse(array('status'=>'ok'), 200);
    }
    public function store(StorePostRequest $request)
    {
        $posDTO = $request->toDTO();
        $pos = $this->postService->create($posDTO);
        return $this->showOne(new PostResource($pos));
    }

    public function update(int $id, UpdatePostRequest $request): JsonResponse
    {
        $postDTO = $request->toDTO();
        if ($id !== $postDTO->id) {
            throw new CustomErrorException(Message::INVALID_ID_PARAMETER_WITH_ID_BODY, HttpCodes::HTTP_BAD_REQUEST);
        }
        $post = $this->postService->update($id, $postDTO);
        return $this->showOne(new PostResource($post));
    }
    public function destroy(int $id): JsonResponse
    {
        $this->postService->delete($id);
        return $this->noContentResponse();
    }
}
