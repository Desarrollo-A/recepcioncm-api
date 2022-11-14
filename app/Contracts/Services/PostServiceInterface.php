<?php
    namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\PostDTO;
use App\Models\Post;

    interface PostServiceInterface extends BaseServiceInterface
    {
        public function create(PostDTO $dto): Post;

        public function update(int $id, PostDTO $dto): Post;
    }

?>