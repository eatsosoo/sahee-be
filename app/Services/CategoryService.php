<?php

namespace App\Services;

use App\Enums\ErrorCodes;
use App\Exceptions\Business\ActionFailException;
use App\Exceptions\DB\CannotDeleteRecordException;
use App\Exceptions\DB\CannotSaveToDBException;
use App\Exceptions\DB\RecordIsNotFoundException;
use App\Helpers\Common\CommonHelper;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService extends BaseService
{
    /** @var CategoryRepository */
    protected CategoryRepository $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo) {
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * get categories
     *
     * @return Collection
     */
    public function searchCategories($rawConditions, $paging)
    {
        try {
            $query = $this->categoryRepo->search();

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
     * create category
     *
     * @param array<string,string> $request
     * @return Category
     * @throws ActionFailException
     */
    public function create($categoryData)
    {
        DB::beginTransaction();
        try {
            $category = $this->categoryRepo->create($categoryData);

            if (is_null($category)) {
                throw new CannotSaveToDBException(ErrorCodes::ERR_CANNOT_CREATE_RECORD);
            }

            DB::commit();
            return $category;
        } catch (Exception $ex) {
            DB::rollBack();
            throw new ActionFailException(previous: $ex);
        }
    }

    /**
     * update category
     *
     * @param array<string,mixed> $categoryData
     * @return Category
     * @throws ActionFailException
     */
    public function update($categoryData)
    {
        DB::beginTransaction();
        try {
            $category = $this->categoryRepo->update($categoryData);

            if (is_null($category)) {
                throw new CannotSaveToDBException(ErrorCodes::ERR_CANNOT_UPDATE_RECORD);
            }

            DB::commit();
            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            throw new ActionFailException(
                'updateCategory: ' . json_encode($categoryData),
                $e->getMessage(),
                $e
            );
        }
    }

    /**
     * delete category
     *
     * @param string $deleteId
     * @return bool
     * @throws ActionFailException
     */
    public function delete($deleteId): bool
    {
        DB::beginTransaction();
        try {
            $record = Category::find($deleteId);
            if (is_null($record)) {
                throw new RecordIsNotFoundException();
            }

            $isDeleted = $this->categoryRepo->delete($deleteId);
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
     * get category detail
     *
     * @param int|string $categoryId
     * @return Category
     * @throws ActionFailException
     */
    public function getCategory($categoryId)
    {
        try {
            $category = $this->categoryRepo->getSingleObject($categoryId);
            if (is_null($category)) {
                throw new RecordIsNotFoundException(ErrorCodes::ERR_RECORD_NOT_FOUND);
            }

            return $category;
        } catch (Exception $e) {
            throw new ActionFailException(
                'getCategory: ' . $categoryId,
                null,
                $e
            );
        }
    }
}