<?php

namespace App\Http\Controllers;

use App\DataResources\BaseDataResource;
use App\DataResources\PostResource;
use App\Helpers\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\SearchPostsRequest;
use App\Services\PostService;

class PostController extends Controller
{
    /** @var PostService */
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * search posts
     *
     * @param SearchPostsRequest $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidPaginationInfoException
     */
    public function search(SearchPostsRequest $request)
    {
        // 1. get validated payload
        $postData = $request->input();

        // 2. get pagination if any
        $paging = null;
        if (isset($postData['pagination'])) {
            $paging = $request->getPaginationInfo();
        }

        // 3. Call business processes
        $postList = $this->postService->searchPosts($postData, $paging);

        // 4. Convert result to output resource
        $result = BaseDataResource::generateResources($postList, PostResource::class);

        // 5. Send response using the predefined format
        if (is_null($paging)) {
            return ApiResponse::v1()
                ->send($result, 'posts');
        } else {
            return ApiResponse::v1()
                ->withTotalPages($paging->last_page, $paging->total)
                ->send($result, 'posts');
        }
    }
}
