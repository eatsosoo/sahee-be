<?php

namespace App\Helpers\Filters;

class BasicFilter
{
    public mixed $conditions;

    public mixed $orders;

    public mixed $limit;

    public mixed $skip;

    public mixed $detail;

    public function __construct()
    {
        $this->conditions = [];
        $this->orders = [];
        $this->detail = [];
        $this->limit = null;
        $this->skip = null;
    }
}
