<?php
    namespace App\Services;

use App\Contracts\Repositories\PostRepositoryInterface;
use App\Contracts\Services\PostServiceInterface;
use App\Core\BaseService;
use App\Models\Dto\PostDTO;
use App\Models\Post;
use Google\Service\ShoppingContent\Resource\Pos;

    class PostService extends BaseService implements PostServiceInterface
    {
        protected $entityRepository;

        public function __construct(PostRepositoryInterface $postRepository)
        {
            $this->entityRepository = $postRepository;
        }
        public function create(PostDTO $dto): Post
        {
            return $this->entityRepository->create($dto->toArray(['title', 'body']));
        }
        public function update(int $id, PostDTO $dto): Post
        {
            return $this->entityRepository->update($id, $dto->toArray(['title', 'body']));
        }
    }
?>