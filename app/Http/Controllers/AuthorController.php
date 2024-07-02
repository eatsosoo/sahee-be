<?php

namespace App\Http\Controllers;

use App\DataResources\BaseDataResource;
use App\DataResources\AuthorResource;
use App\Exceptions\Business\ActionFailException;
use App\Exceptions\Request\InvalidPaginationInfoException;
use App\Helpers\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Author\SearchAuthorRequest;
use App\Http\Requests\Author\StoreAuthorRequest;
use App\Http\Requests\Author\UpdateAuthorRequest;
use App\Services\AuthorService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthorController extends Controller
{
    /** @var AuthorService */
    protected AuthorService $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
    }

    /**
     * search authors
     *
     * @param SearchAuthorRequest $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidPaginationInfoException
     */
    public function search(SearchAuthorRequest $request)
    {
        // 1. get validated payload
        $authorData = $request->input();

        // 2. get pagination if any
        $paging = null;
        if (isset($authorData['pagination'])) {
            $paging = $request->getPaginationInfo();
        }

        // 3. Call business processes
        $authorList = $this->authorService->searchCategories($authorData, $paging);

        // 4. Convert result to output resource
        $result = BaseDataResource::generateResources($authorList, AuthorResource::class);

        // 5. Send response using the predefined format
        if (is_null($paging)) {
            return ApiResponse::v1()
                ->send($result, 'authors');
        } else {
            return ApiResponse::v1()
                ->withTotalPages($paging->last_page, $paging->total)
                ->send($result, 'authors');
        }
    }

    /**
     * create author
     *
     * @param StoreAuthorRequest $request
     * @return Response
     * @throws ActionFailException
     */
    public function create(StoreAuthorRequest $request)
    {
        $authorCreated = $this->authorService->create($request->all());
        $result = new AuthorResource($authorCreated);

        return ApiResponse::v1()->send($result, dataKey: 'author');
    }

    /**
     * update author
     *
     * @param UpdateAuthorRequest $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidModelInstanceException
     */
    public function update(UpdateAuthorRequest $request)
    {
        // 1. get validated payload
        $authorData = $request->all();

        // 2. Call business processes
        $author = $this->authorService->update($authorData);
        $message = __('message.edit_success');

        // 3. Convert result to output resource
        $result = new AuthorResource($author);

        // 4. Send response using the predefined format
        return ApiResponse::v1()->withMessage($message)->send($result, 'author');
    }

    /**
     * delete author
     *
     * @param Request $request
     * @return Response
     * @throws ActionFailException
     */
    public function delete($requestId)
    {
        $result = $this->authorService->delete($requestId);
        $message = __('message.delete_success');
        return ApiResponse::v1()->report($result, $message);
    }

    /**
     * detail author
     *
     * @param Request $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidModelInstanceException
     */
    public function getAuthor(Request $request)
    {
        // 1. Get author template id
        $authorId = $request->id;

        // 2. Call business processes
        $author = $this->authorService->getAuthor($authorId);

        // 3. Convert result to output resource
        $result = new AuthorResource($author);

        // 4. Send response using the predefined format
        return ApiResponse::v1()->send($result, 'author');
    }
}
