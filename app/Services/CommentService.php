<?php

namespace App\Services;

use App\Enums\ErrorCodes;
use App\Exceptions\Business\ActionFailException;
use App\Exceptions\DB\CannotDeleteRecordException;
use App\Exceptions\DB\CannotSaveToDBException;
use App\Exceptions\DB\RecordIsNotFoundException;
use App\Helpers\Common\CommonHelper;
use App\Models\Comment;
use App\Repositories\CommentRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentService extends BaseService
{
    /** @var CommentRepository */
    protected CommentRepository $commentRepo;

    public function __construct(CommentRepository $commentRepo)
    {
        $this->commentRepo = $commentRepo;
    }

    /**
     * get comments
     *
     * @return Collection
     */
    public function searchComments($rawConditions, $paging)
    {
        try {
            $query = $this->commentRepo->search();

            if (isset($rawConditions['book_id'])) {
                $param = CommonHelper::escapeLikeQueryParameter($rawConditions['book_id']);
                $query = $this->commentRepo->queryOnAField(['book_id', $param]);
            }

            if (isset($rawConditions['sort'])) {
                $query = $query->orderBy($rawConditions['sort']['field'], $rawConditions['sort']['order']);
            }

            if (!is_null($paging)) {
                $paginator = $this->applyPagination($query, $paging);
                return $paginator->items();
            }

            return $this->commentRepo->withs(['user'], $query)->get()->all();
        } catch (Exception $e) {
            Log::error('SearchComments: ' . $e->getMessage());
            throw new ActionFailException(
                'SearchComments: ' . json_encode(['conditions' => $rawConditions, 'pagination' => $paging]),
                null,
                $e
            );
        }
    }

    /**
     * delete comment
     *
     * @param string $deleteId
     * @return bool
     * @throws ActionFailException
     */
    public function delete($deleteId): bool
    {
        DB::beginTransaction();
        try {
            $record = Comment::find($deleteId);
            if (is_null($record)) {
                throw new RecordIsNotFoundException();
            }

            $isDeleted = $this->commentRepo->delete($deleteId);
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
     * create comment
     *
     * @param array<string,string> $request
     * @return Comment
     * @throws ActionFailException
     */
    public function create($commentData)
    {
        DB::beginTransaction();
        try {
            $comment = $this->commentRepo->create($commentData);

            if (is_null($comment)) {
                throw new CannotSaveToDBException(ErrorCodes::ERR_CANNOT_CREATE_RECORD);
            }

            DB::commit();
            return $comment;
        } catch (Exception $ex) {
            DB::rollBack();
            throw new ActionFailException(previous: $ex);
        }
    }

    /**
     * update comment
     *
     * @param array<string,string> $request
     * @return Comment
     * @throws ActionFailException
     */
    public function update($commentData)
    {
        DB::beginTransaction();
        try {
            $comment = $this->commentRepo->update($commentData);

            if (is_null($comment)) {
                throw new CannotSaveToDBException(ErrorCodes::ERR_CANNOT_CREATE_RECORD);
            }

            DB::commit();
            return $comment;
        } catch (Exception $ex) {
            DB::rollBack();
            throw new ActionFailException(previous: $ex);
        }
    }
}
