<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository extends BaseRepository
{
    /**
     * get corresponding model class name
     *
     * @return string
     */
    public function getRepositoryModelClass(): string
    {
        return Post::class;
    }

    /**
     * get posts list
     *
     * @param array<mixed> $condition
     * @return Post[]
     */
    public function getPosts($condition = [])
    {
        return Post::with(['user', 'comments'])->get();
    }
}
