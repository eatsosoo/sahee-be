<?php

namespace App\Repositories;

use App\Models\Author;

class AuthorRepository extends BaseRepository
{
    /**
     * get corresponding model class name
     *
     * @return string
     */
    public function getRepositoryModelClass(): string
    {
        return Author::class;
    }
}
