<?php
    namespace App\Repositories;

    use App\Contracts\Repositories\PostRepositoryInterface;
    use App\Core\BaseRepository;
    use App\Models\Post;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Query\Builder as QueryBuilder;

    class PostRepository extends BaseRepository implements PostRepositoryInterface
    {
        /**
         * @var Builder|Model|QueryBuilder
         */
        protected $entity;

        public function __construct(Post $post)
        {
            $this->entity = $post;
        }

        public function findByName(string $name): Post{
            return $this->entity->where('title', $name)->firstOrFail();
        }
    }
    
?>