<?php

namespace App\Services;

use App\DataResources\Extras\PaginationInfo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseService implements IService
{
    /**
     * apply pagination info on query
     *
     * @param Builder $query
     * @param PaginationInfo|null $pagination
     * @return LengthAwarePaginator
     */
    public function applyPagination(Builder $query, PaginationInfo &$pagination = null): LengthAwarePaginator
    {
        $paginator = $query->paginate(perPage: $pagination->perPage, page: intval($pagination->page));
        $pagination->total = $paginator->total();
        $pagination->last_page = $paginator->lastPage();
        return $paginator;
    }
}
