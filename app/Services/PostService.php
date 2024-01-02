<?php

namespace App\Services;

use App\Repositories\PostRepository;

class PostService extends BaseService
{
    /** @var PostRepository */
    protected PostRepository $postRepo;

    public function __construct(PostRepository $postRepo) {
        $this->postRepo = $postRepo;
    }

    /**
     * get posts
     *
     * @return Collection
     */
    public function searchPosts()
    {
        return $this->postRepo->getPosts();
    }
}