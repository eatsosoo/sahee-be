<?php

namespace App\Http\Controllers;

use App\DataResources\BaseDataResource;
use App\DataResources\CategoryResource;
use App\Helpers\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\SearchCategoryRequest;
use App\Services\CategoryService;

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
}
