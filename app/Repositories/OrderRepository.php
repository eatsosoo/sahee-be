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

    /**
     * get orders list
     *
     * @param array<mixed> $rawConditions
     * @param mixed $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function searchOrders($rawConditions, $query)
    {
        // Xử lý các điều kiện tìm kiếm cơ bản
        if (!empty($rawConditions['order_code'])) {
            $query->where('order_code', 'like', '%' . $rawConditions['order_code'] . '%');
        }
        if (!empty($rawConditions['customer_name'])) {
            $query->where('customer_name', 'like', '%' . $rawConditions['customer_name'] . '%');
        }
        if (!empty($rawConditions['customer_phone'])) {
            $query->where('customer_phone', 'like', '%' . $rawConditions['customer_phone'] . '%');
        }
        if (!empty($rawConditions['status'])) {
            $query->where('status', '=', $rawConditions['status']);
        }
        if (!empty($rawConditions['from']) && !empty($rawConditions['to'])) {
            $query->whereBetween('created_at', [$rawConditions['from'], $rawConditions['to']]);
        }

        // Join với order_items và books để tìm kiếm theo tên sách
        if (!empty($rawConditions['book_name'])) {
            $query->whereHas('items.book', function ($q) use ($rawConditions) {
                $q->where('name', 'like', '%' . $rawConditions['book_name'] . '%');
            });
        }

        return $query;
    }

    /**
     * Calculate monthly revenue
     *
     * @param int $year
     * @param int $month
     * @param int|null $day
     * @return float
     */
    public function calculateRevenue(int $year, int $month, int $day = null): float
    {
        if ($day) {
            return Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereDay('created_at', $day)
                ->sum('total_amount');
        }

        return Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('total_amount');
    }


    /**
     * Calculate yearly revenue
     *
     * @param int $year
     * @return float
     */
    public function monthlyRevenue(int $year)
    {
        $revenue = [];
        for ($month = 1; $month <= 12; $month++) {
            $revenue[] = [
                'month' => $month,
                'total' => $this->calculateRevenue($year, $month)
            ];
        }
        return $revenue;
    }

    /**
     * Calculate daily revenue
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public function dailyRevenue(int $year, int $month): array
    {
        $revenue = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $revenue[] = [
                'day' => $day,
                'total' => $this->calculateRevenue($year, $month, $day)
            ];
        }
        return $revenue;
    }

    /**
     * Calculate weekly revenue
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public function weeklyRevenue(int $year, int $month): array
    {
        $revenue = [];
        $firstDayOfMonth = date('Y-m-01', strtotime("$year-$month-01"));
        $lastDayOfMonth = date('Y-m-t', strtotime("$year-$month-01"));
        $startOfWeek = $firstDayOfMonth;
        $endOfWeek = date('Y-m-d', strtotime("$startOfWeek +6 days"));

        while ($startOfWeek <= $lastDayOfMonth) {
            $revenue[] = [
                'start_date' => $startOfWeek,
                'end_date' => $endOfWeek,
                'total' => $this->calculateRevenueByRange($startOfWeek, $endOfWeek)
            ];

            $startOfWeek = date('Y-m-d', strtotime("$endOfWeek +1 day"));
            $endOfWeek = date('Y-m-d', strtotime("$startOfWeek +6 days"));
            $endOfWeek = $endOfWeek > $lastDayOfMonth ? $lastDayOfMonth : $endOfWeek;
        }

        return $revenue;
    }

    /**
     * Calculate revenue by date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public function calculateRevenueByRange(string $startDate, string $endDate): float
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');
    }
}
