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
     * @param array<mixed> $rawConditions
     * @param mixed $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function searchBooks($rawConditions, $query)
    {
        $conditions = [];
        if (!empty($rawConditions['name'])) {
            $conditions[] = ['name', 'like', '%' . $rawConditions['name'] . '%'];
        }
        
        if (!empty($rawConditions['category_id'])) {
            $conditions[] = ['category_id', '=', $rawConditions['category_id']];
        }

        if (!empty($rawConditions['stock'])) {
            $conditions[] = ['stock', '<=', $rawConditions['stock']];
        }
        
        $query = $query->where($conditions);

        if (!empty($rawConditions['author'])) {
            $query = $query->whereHas('author', function ($query) use ($rawConditions) {
                $query->where('name', 'like', '%' . $rawConditions['author'] . '%');
            });
        }

        return $query;
    }

    /**
     * get book by id
     *
     * @param int $id
     * @return void
     */
    public function updateStock($bookId, $quantity)
    {
        $book = Book::find($bookId);
        $book->stock -= $quantity;
        $book->save();
    }
}
