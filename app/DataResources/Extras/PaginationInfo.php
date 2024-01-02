<?php

namespace App\DataResources\Extras;

use App\Exceptions\Request\InvalidPaginationInfoException;
use PHPUnit\Exception;

/**
 * @property int $page
 * @property int $perPage
 * @property int $last_page
 * @property int $total
 */
class PaginationInfo
{
    public int $page;
    public int $perPage;
    public int $lastPages;
    public int $total;

    public function __construct()
    {
        $this->page = 1;
        $this->perPage = env('PAGINATION_DEFAULT_PER_PAGE', 25);
    }

    /**
     * parse paginate
     *
     * @param array<mixed> $param
     * @return ?PaginationInfo
     * @throws InvalidPaginationInfoException
     */
    public static function parse(array $param): ?PaginationInfo
    {
        try {
            $ret = new PaginationInfo();
            $info = $param; //$param['pagination'];
            if (in_array('page', array_keys($info))) {
                $c = $info['page'];
                $ret->page = $c > 0 ? $c : $ret->page;
            }
            if (in_array('per_page', array_keys($info))) {
                $c = $info['per_page'];
                $ret->perPage = $c > 0 ? $c : $ret->perPage;
            }
            return $ret;
        } catch (Exception $ex) {
            throw new InvalidPaginationInfoException(previous: $ex);
        }
    }
}
