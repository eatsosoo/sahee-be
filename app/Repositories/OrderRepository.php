<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository extends BaseRepository
{
    /**
     * get corresponding model class name
     *
     * @return string
     */
    public function getRepositoryModelClass(): string
    {
        return Order::class;
    }

    /**
     * get orders list
     *
     * @param array<mixed> $condition
     * @return Order[]
     */
    public function getOrders($condition = [])
    {
        return Order::with(['user', 'comments'])->get();
    }
}
