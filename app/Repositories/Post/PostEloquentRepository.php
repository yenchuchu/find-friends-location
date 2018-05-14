<?php
namespace App\Repositories\Post;

use App\Web\Post;
use App\Repositories\RepositoryAbstract;

class PostEloquentRepository extends RepositoryAbstract implements PostRepositoryInterface
{
// Class thực hiện tất cả các function được định nghĩa trong PostRepositoryInterface và thừa kế các function
//được định nghĩa trong RepositoryAbstract

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Post::class;
    }

    public function getAllUser( ) {
        return $this->_model::all();
    }
}