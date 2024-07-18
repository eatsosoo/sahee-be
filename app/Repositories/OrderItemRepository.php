<?php

namespace App\Repositories;

use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderItemRepository extends BaseRepository
{
    /**
     * get corresponding model class name
     *
     * @return string
     */
    public function getRepositoryModelClass(): string
    {
        return OrderItem::class;
    }

    // ...

    public function findTopSixBookIds(): array
    {
        $bookIds = DB::table('order_items')
            ->select('book_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('book_id')
            ->orderByDesc('total_quantity')
            ->limit(6)
            ->pluck('book_id')
            ->toArray();

        return $bookIds;
    }
}
