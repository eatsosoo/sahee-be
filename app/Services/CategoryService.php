<?php

namespace App\Services;

use App\Exceptions\Business\ActionFailException;
use App\Helpers\Common\CommonHelper;
use App\Repositories\CategoryRepository;
use Exception;
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
                $param = CommonHelper::escapeLikeQueryParameter($rawConditions['name']);
                $query = $this->categoryRepo->queryOnAField(['name', $param]);
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
}