<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository extends BaseRepository
{
    /**
     * get corresponding model class name
     *
     * @return string
     */
    public function getRepositoryModelClass(): string
    {
        return Comment::class;
    }

    /**
     * get books list
     *
     * @param array<mixed> $condition
     * @return Comment[]
     */
    public function getComments($condition = [])
    {
        return Comment::with(['user'])->get();
    }
}
