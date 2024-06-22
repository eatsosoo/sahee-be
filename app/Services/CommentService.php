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

            if (isset($rawConditions['user_id'])) {
                $param = CommonHelper::escapeLikeQueryParameter($rawConditions['user_id']);
                $query = $this->commentRepo->queryOnAField(['user_id', $param]);
            }

            if (isset($rawConditions['order_id'])) {
                $param = CommonHelper::escapeLikeQueryParameter($rawConditions['order_id']);
                $query = $this->commentRepo->queryOnAField(['order_id', $param]);
            }

            if (isset($rawConditions['sort'])) {
                $query = $query->orderBy($rawConditions['sort']['key'], $rawConditions['sort']['order']);
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

    /**
     * Calculate average star rating, total number of comments, and total number of each star rating.
     *
     * @param string $bookId
     * @return array
     */
    public function calculateStatistics($bookId)
    {
        try {
            $query = $this->commentRepo->search();

            if ($bookId) {
                $param = CommonHelper::escapeLikeQueryParameter($bookId);
                $query = $this->commentRepo->queryOnAField(['book_id', $param]);
            }

            $comments = $query->get()->all();
            $totalComments = count($comments);
            $totalRatings = 0;
            $starRatings = [0, 0, 0, 0, 0]; // Initialize array to store count of each star rating

            foreach ($comments as $comment) {
                $rating = $comment->star;
                $totalRatings += $rating;
                $starRatings[$rating - 1]++; // Increment count for corresponding star rating
            }

            $averageRating = round($totalRatings / $totalComments, 2);

            return [
                'average_rating' => $averageRating,
                'total_comments' => $totalComments,
                'star_ratings' => $starRatings
            ];
        } catch (Exception $e) {
            Log::error('CalculateStatistics: ' . $e->getMessage());
            throw new ActionFailException('CalculateStatistics: Failed to calculate statistics.', null, $e);
        }
    }

    /**
     * Find a comment by order_id, book_id, and user_id.
     *
     * @param string $orderId
     * @param string $bookId
     * @param string $userId
     * @return Comment|null
     */
    public function findComment($orderId, $bookId, $userId)
    {
        try {
            $query = $this->commentRepo->search();
            $query->where('order_id', $orderId)
                ->where('book_id', $bookId)
                ->where('user_id', $userId);
                
            return $query->first();
        } catch (Exception $e) {
            Log::error('FindComment: ' . $e->getMessage());
            throw new ActionFailException('FindComment: Failed to find comment.', null, $e);
        }
    }
}
