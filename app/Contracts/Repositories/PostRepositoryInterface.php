<?php
    namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Post;

    interface PostRepositoryInterface extends BaseRepositoryInterface{

        public function findByName(string $name): Post;
    }

?>