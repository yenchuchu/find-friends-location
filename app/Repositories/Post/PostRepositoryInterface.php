<?php
namespace App\Repositories\Post;

interface PostRepositoryInterface
{
    /**
     * Get all posts only published
     * @return mixed
     */
    public function getAllUser();

}
?>