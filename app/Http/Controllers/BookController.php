<?php

namespace App\Http\Controllers;

use App\DataResources\BaseDataResource;
use App\DataResources\BookResource;
use App\Helpers\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\SearchBooksRequest;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /** @var BookService */
    protected BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * search books
     *
     * @param SearchBooksRequest $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidPaginationInfoException
     */
    public function search(SearchBooksRequest $request)
    {
        // 1. get validated payload
        $bookData = $request->input();

        // 2. get pagination if any
        $paging = null;
        if (isset($bookData['pagination'])) {
            $paging = $request->getPaginationInfo();
        }

        // 3. Call business processes
        $bookList = $this->bookService->searchBooks($bookData, $paging);

        // 4. Convert result to output resource
        $result = BaseDataResource::generateResources($bookList, BookResource::class);

        // 5. Send response using the predefined format
        if (is_null($paging)) {
            return ApiResponse::v1()
                ->send($result, 'books');
        } else {
            return ApiResponse::v1()
                ->withTotalPages($paging->last_page, $paging->total)
                ->send($result, 'books');
        }
    }

    /**
     * create book
     *
     * @param StoreBookRequest $request
     * @return Response
     * @throws ActionFailException
     */
    public function create(StoreBookRequest $request)
    {
        $bookCreated = $this->bookService->create($request->all());
        $result = new BookResource($bookCreated);

        return ApiResponse::v1()->send($result, dataKey: 'book');
    }

    /**
     * update book
     *
     * @param UpdateBookRequest $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidModelInstanceException
     */
    public function update(UpdateBookRequest $request)
    {
        // 1. get validated payload
        $bookData = $request->all();

        // 2. Call business processes
        $mailBlock = $this->bookService->update($bookData);
        $message = __('message.edit_success');

        // 3. Convert result to output resource
        $result = new BookResource($mailBlock);

        // 4. Send response using the predefined format
        return ApiResponse::v1()->withMessage($message)->send($result, 'book');
    }

    /**
     * delete book
     *
     * @param Request $request
     * @return Response
     * @throws ActionFailException
     */
    public function delete($requestId)
    {
        $result = $this->bookService->delete($requestId);
        $message = __('message.delete_success');
        return ApiResponse::v1()->report($result, $message);
    }

    /**
     * detail book
     *
     * @param Request $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidModelInstanceException
     */
    public function getBook(Request $request)
    {
        // 1. Get book template id
        $bookId = $request->id;

        // 2. Call business processes
        $book = $this->bookService->getBook($bookId);

        // 3. Convert result to output resource
        $result = new BookResource($book);

        // 4. Send response using the predefined format
        return ApiResponse::v1()->send($result, 'book');
    }
}
