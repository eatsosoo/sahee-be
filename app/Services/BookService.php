<?php

namespace App\Services;

use App\Enums\ErrorCodes;
use App\Exceptions\Business\ActionFailException;
use App\Exceptions\DB\CannotDeleteRecordException;
use App\Exceptions\DB\CannotSaveToDBException;
use App\Exceptions\DB\RecordIsNotFoundException;
use App\Helpers\Common\CommonHelper;
use App\Models\Book;
use App\Repositories\BookRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public function searchBooks($rawConditions, $paging)
    {
        try {
            $query = $this->bookRepo->search();

            if (isset($rawConditions['name'])) {
                $param = CommonHelper::escapeLikeQueryParameter($rawConditions['name']);
                $query = $this->bookRepo->queryOnAField(['name', $param]);
            }

            if (isset($rawConditions['sort'])) {
                $query = $query->orderBy($rawConditions['sort']['key'], $rawConditions['sort']['order']);
            }

            if (!is_null($paging)) {
                $paginator = $this->applyPagination($query, $paging);
                return $paginator->items();
            }

            return $this->bookRepo->withs(['user'], $query)->get()->all();
        } catch (Exception $e) {
            Log::error('SearchBooks: ' . $e->getMessage());
            throw new ActionFailException(
                'SearchBooks: ' . json_encode(['conditions' => $rawConditions, 'pagination' => $paging]),
                null,
                $e
            );
        }
    }

    /**
     * create book
     *
     * @param array<string,string> $request
     * @return Book
     * @throws ActionFailException
     */
    public function create($bookData)
    {
        DB::beginTransaction();
        try {
            $book = $this->bookRepo->create($bookData);

            if (is_null($book)) {
                throw new CannotSaveToDBException(ErrorCodes::ERR_CANNOT_CREATE_RECORD);
            }

            DB::commit();
            return $book;
        } catch (Exception $ex) {
            DB::rollBack();
            throw new ActionFailException(previous: $ex);
        }
    }

    /**
     * delete book
     *
     * @param string $deleteId
     * @return bool
     * @throws ActionFailException
     */
    public function delete($deleteId): bool
    {
        DB::beginTransaction();
        try {
            $record = Book::find($deleteId);
            if (is_null($record)) {
                throw new RecordIsNotFoundException();
            }

            $isDeleted = $this->bookRepo->delete($deleteId);
            if (!$isDeleted) {
                throw new CannotDeleteRecordException();
            }

            DB::commit();

            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            throw new ActionFailException(previous: $ex);
        }
    }
}