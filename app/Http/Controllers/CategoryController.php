<?php

namespace App\Http\Controllers;

use App\DataResources\BaseDataResource;
use App\DataResources\CategoryResource;
use App\Exceptions\Business\ActionFailException;
use App\Exceptions\Request\InvalidPaginationInfoException;
use App\Helpers\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\SearchCategoryRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /** @var CategoryService */
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * search categories
     *
     * @param SearchCategoryRequest $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidPaginationInfoException
     */
    public function search(SearchCategoryRequest $request)
    {
        // 1. get validated payload
        $categoryData = $request->input();

        // 2. get pagination if any
        $paging = null;
        if (isset($categoryData['pagination'])) {
            $paging = $request->getPaginationInfo();
        }

        // 3. Call business processes
        $categoryList = $this->categoryService->searchCategories($categoryData, $paging);

        // 4. Convert result to output resource
        $result = BaseDataResource::generateResources($categoryList, CategoryResource::class);

        // 5. Send response using the predefined format
        if (is_null($paging)) {
            return ApiResponse::v1()
                ->send($result, 'categories');
        } else {
            return ApiResponse::v1()
                ->withTotalPages($paging->last_page, $paging->total)
                ->send($result, 'categories');
        }
    }

    /**
     * create category
     *
     * @param StoreCategoryRequest $request
     * @return Response
     * @throws ActionFailException
     */
    public function create(StoreCategoryRequest $request)
    {
        $categoryCreated = $this->categoryService->create($request->all());
        $result = new CategoryResource($categoryCreated);

        return ApiResponse::v1()->send($result, dataKey: 'category');
    }

    /**
     * update category
     *
     * @param UpdateCategoryRequest $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidModelInstanceException
     */
    public function update(UpdateCategoryRequest $request)
    {
        // 1. get validated payload
        $categoryData = $request->all();

        // 2. Call business processes
        $category = $this->categoryService->update($categoryData);
        $message = __('message.edit_success');

        // 3. Convert result to output resource
        $result = new CategoryResource($category);

        // 4. Send response using the predefined format
        return ApiResponse::v1()->withMessage($message)->send($result, 'category');
    }

    /**
     * delete category
     *
     * @param Request $request
     * @return Response
     * @throws ActionFailException
     */
    public function delete($requestId)
    {
        $result = $this->categoryService->delete($requestId);
        $message = __('message.delete_success');
        return ApiResponse::v1()->report($result, $message);
    }

    /**
     * detail category
     *
     * @param Request $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidModelInstanceException
     */
    public function getCategory(Request $request)
    {
        // 1. Get category template id
        $categoryId = $request->id;

        // 2. Call business processes
        $category = $this->categoryService->getCategory($categoryId);

        // 3. Convert result to output resource
        $result = new CategoryResource($category);

        // 4. Send response using the predefined format
        return ApiResponse::v1()->send($result, 'category');
    }
}
