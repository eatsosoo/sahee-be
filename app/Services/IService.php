<?php

namespace App\Services;

use App\DataResources\Extras\PaginationInfo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

interface IService
{
    /**
     * apply pagination info on query
     */
    public function applyPagination(Builder $query, PaginationInfo &$pagination = null): LengthAwarePaginator;
}
