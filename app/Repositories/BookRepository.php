<?php

namespace App\Repositories;

use App\Models\Book;

class BookRepository extends BaseRepository
{
    /**
     * get corresponding model class name
     *
     * @return string
     */
    public function getRepositoryModelClass(): string
    {
        return Book::class;
    }

    /**
     * get books list
     *
     * @param array<mixed> $condition
     * @return Book[]
     */
    public function getBooks($condition = [])
    {
        return Book::with(['user', 'comments'])->get();
    }
}
