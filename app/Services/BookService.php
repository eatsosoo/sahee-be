<?php

namespace App\Services;

use App\Repositories\BookRepository;

class BookService extends BaseService
{
    /** @var BookRepository */
    protected BookRepository $bookRepo;

    public function __construct(BookRepository $bookRepo) {
        $this->bookRepo = $bookRepo;
    }

    /**
     * get books
     *
     * @return Collection
     */
    public function searchBooks()
    {
        return $this->bookRepo->getBooks();
    }
}