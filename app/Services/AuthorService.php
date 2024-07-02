<?php

namespace App\Services;

use App\Enums\ErrorCodes;
use App\Exceptions\Business\ActionFailException;
use App\Exceptions\DB\CannotDeleteRecordException;
use App\Exceptions\DB\CannotSaveToDBException;
use App\Exceptions\DB\RecordIsNotFoundException;
use App\Helpers\Common\CommonHelper;
use App\Models\Author;
use App\Repositories\AuthorRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthorService extends BaseService
{
    /** @var AuthorRepository */
    protected AuthorRepository $authorRepo;

    public function __construct(AuthorRepository $authorRepo) {
        $this->authorRepo = $authorRepo;
    }

    /**
     * get categories
     *
     * @return Collection
     */
    public function searchCategories($rawConditions, $paging)
    {
        try {
            $query = $this->authorRepo->search();

            if (isset($rawConditions['name'])) {
                $query->where('name', 'like', '%' . $rawConditions['name'] . '%');
            }

            if (isset($rawConditions['sort'])) {
                $query = $query->orderBy($rawConditions['sort']['key'], $rawConditions['sort']['order']);
            }

            if (!is_null($paging)) {
                $paginator = $this->applyPagination($query, $paging);
                return $paginator->items();
            }

            return $query->get()->all();
        } catch (Exception $e) {
            Log::error('SearchCategories: ' . $e->getMessage());
            throw new ActionFailException(
                'SearchCategories: ' . json_encode(['conditions' => $rawConditions, 'pagination' => $paging]),
                null,
                $e
            );
        }
    }

    /**
     * create author
     *
     * @param array<string,string> $request
     * @return Author
     * @throws ActionFailException
     */
    public function create($authorData)
    {
        DB::beginTransaction();
        try {
            $author = $this->authorRepo->create($authorData);

            if (is_null($author)) {
                throw new CannotSaveToDBException(ErrorCodes::ERR_CANNOT_CREATE_RECORD);
            }

            DB::commit();
            return $author;
        } catch (Exception $ex) {
            DB::rollBack();
            throw new ActionFailException(previous: $ex);
        }
    }

    /**
     * update author
     *
     * @param array<string,mixed> $authorData
     * @return Author
     * @throws ActionFailException
     */
    public function update($authorData)
    {
        DB::beginTransaction();
        try {
            $author = $this->authorRepo->update($authorData);

            if (is_null($author)) {
                throw new CannotSaveToDBException(ErrorCodes::ERR_CANNOT_UPDATE_RECORD);
            }

            DB::commit();
            return $author;
        } catch (Exception $e) {
            DB::rollBack();
            throw new ActionFailException(
                'updateAuthor: ' . json_encode($authorData),
                $e->getMessage(),
                $e
            );
        }
    }

    /**
     * delete author
     *
     * @param string $deleteId
     * @return bool
     * @throws ActionFailException
     */
    public function delete($deleteId): bool
    {
        DB::beginTransaction();
        try {
            $record = Author::find($deleteId);
            if (is_null($record)) {
                throw new RecordIsNotFoundException();
            }

            $isDeleted = $this->authorRepo->delete($deleteId);
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

    /**
     * get author detail
     *
     * @param int|string $authorId
     * @return Author
     * @throws ActionFailException
     */
    public function getAuthor($authorId)
    {
        try {
            $author = $this->authorRepo->getSingleObject($authorId);
            if (is_null($author)) {
                throw new RecordIsNotFoundException(ErrorCodes::ERR_RECORD_NOT_FOUND);
            }

            return $author;
        } catch (Exception $e) {
            throw new ActionFailException(
                'getAuthor: ' . $authorId,
                null,
                $e
            );
        }
    }
}