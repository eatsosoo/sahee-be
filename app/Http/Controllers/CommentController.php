<?php

namespace App\Http\Controllers;

use App\DataResources\BaseDataResource;
use App\DataResources\CommentResource;
use App\Helpers\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\SearchCommentsRequest;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Services\CommentService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /** @var CommentService */
    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * search comments
     *
     * @param SearchCommentsRequest $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidPaginationInfoException
     */
    public function search(SearchCommentsRequest $request)
    {
        // 1. get validated payload
        $commentData = $request->input();

        // 2. get pagination if any
        $paging = null;
        if (isset($commentData['pagination'])) {
            $paging = $request->getPaginationInfo();
        }

        // 3. Call business processes
        $commentList = $this->commentService->searchComments($commentData, $paging);

        // 4. Convert result to output resource
        $result = BaseDataResource::generateResources($commentList, CommentResource::class);

        // 5. Send response using the predefined format
        if (is_null($paging)) {
            return ApiResponse::v1()
                ->send($result, 'comments');
        } else {
            return ApiResponse::v1()
                ->withTotalPages($paging->last_page, $paging->total)
                ->send($result, 'comments');
        }
    }

    /**
     * create comment
     *
     * @param StoreCommentRequest $request
     * @return Response
     * @throws ActionFailException
     */
    public function create(StoreCommentRequest $request)
    {
        $commentCreated = $this->commentService->create($request->all());
        $result = new CommentResource($commentCreated);

        return ApiResponse::v1()->send($result, dataKey: 'comment');
    }

    /**
     * update comment
     *
     * @param UpdateCommentRequest $request
     * @return Response
     * @throws ActionFailException
     */
    public function update(UpdateCommentRequest $request)
    {
        $commentUpdated = $this->commentService->update($request->all());
        $result = new CommentResource($commentUpdated);

        return ApiResponse::v1()->send($result, dataKey: 'comment');
    }

    /**
     * delete comment
     *
     * @param Request $request
     * @return Response
     * @throws ActionFailException
     */
    public function delete($requestId)
    {
        $result = $this->commentService->delete($requestId);
        $message = __('message.delete_success');
        return ApiResponse::v1()->report($result, $message);
    }

    /**
     * get rating
     *
     * @param Request $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidModelInstanceException
     */
    public function rating(Request $request)
    {
        // 1. Get category template id
        $book = $request->id;

        // 2. Call business processes
        $rating = $this->commentService->calculateStatistics($book);

        // 3. Send response using the predefined format
        return ApiResponse::v1()->send($rating, 'rating');
    }
}
