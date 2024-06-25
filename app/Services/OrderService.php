<?php

namespace App\Services;

use App\Enums\ErrorCodes;
use App\Exceptions\Business\ActionFailException;
use App\Exceptions\DB\CannotDeleteRecordException;
use App\Exceptions\DB\CannotSaveToDBException;
use App\Exceptions\DB\RecordIsNotFoundException;
use App\Helpers\Common\CommonHelper;
use App\Models\Book;
use App\Models\Order;
use App\Repositories\BookRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService extends BaseService
{
    /** @var OrderRepository */
    protected OrderRepository $orderRepo;
    /** @var OrderItemRepository */
    protected OrderItemRepository $orderItemRepo;
    /** @var BookRepository */
    protected BookRepository $bookRepo;

    public function __construct(OrderRepository $orderRepo, OrderItemRepository $orderItemRepo, BookRepository $bookRepo)
    {
        $this->orderRepo = $orderRepo;
        $this->orderItemRepo = $orderItemRepo;
        $this->bookRepo = $bookRepo;
    }

    /**
     * get orders
     *
     * @return Collection
     */
    public function searchOrders($rawConditions, $paging)
    {
        try {
            $query = $this->orderRepo->search();

            $conditions = [];
            if (!empty($rawConditions['order_code'])) {
                $conditions[] = ['order_code', 'like', '%' . $rawConditions['order_code'] . '%'];
            }
            if (!empty($rawConditions['customer_name'])) {
                $conditions[] = ['customer_name', 'like', '%' . $rawConditions['customer_name'] . '%'];
            }
            if (!empty($rawConditions['customer_phone'])) {
                $conditions[] = ['customer_phone', 'like', '%' . $rawConditions['customer_phone'] . '%'];
            }
            if (!empty($rawConditions['status'])) {
                $conditions[] = ['status', '=', $rawConditions['status']];
            }
            if (!empty($rawConditions['from']) && !empty($rawConditions['to'])) {
                $conditions[] = ['created_at', '>=', $rawConditions['from']];
                $conditions[] = ['created_at', '<=', $rawConditions['to']];
            }

            $query = $query->where($conditions);

            if (isset($rawConditions['sort'])) {
                $query = $query->orderBy($rawConditions['sort']['key'], $rawConditions['sort']['order']);
            }

            if (!is_null($paging)) {
                $paginator = $this->applyPagination($query, $paging);
                return $paginator->items();
            }

            return $this->orderRepo->withs(['book'], $query)->get()->all();
        } catch (Exception $e) {
            Log::error('SearchOrders: ' . $e->getMessage());
            throw new ActionFailException(
                'SearchOrders: ' . json_encode(['conditions' => $rawConditions, 'pagination' => $paging]),
                null,
                $e
            );
        }
    }

    /**
     * create order
     *
     * @param array<string,string> $request
     * @return Order
     * @throws ActionFailException
     */
    public function create($orderData)
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepo->create($orderData);

            if (is_null($order)) {
                throw new CannotSaveToDBException(ErrorCodes::ERR_CANNOT_CREATE_RECORD);
            }

            foreach ($orderData['items'] as $item) {
                $item['order_id'] = $order['id'];
                $orderItem = $this->orderItemRepo->create($item);
                $this->bookRepo->updateStock($item['book_id'], $item['quantity']);

                if (is_null($orderItem)) {
                    throw new CannotSaveToDBException(ErrorCodes::ERR_CANNOT_CREATE_RELATED_DATA);
                }
            }

            DB::commit();
            return $order;
        } catch (Exception $ex) {
            DB::rollBack();
            throw new ActionFailException(previous: $ex);
        }
    }

    /**
     * update order
     *
     * @param array<string,mixed> $orderData
     * @return Order
     * @throws ActionFailException
     */
    public function update($orderData)
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepo->update($orderData);

            if (is_null($order)) {
                throw new CannotSaveToDBException(ErrorCodes::ERR_CANNOT_UPDATE_RECORD);
            }

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw new ActionFailException(
                'updateOrder: ' . json_encode($orderData),
                $e->getMessage(),
                $e
            );
        }
    }

    /**
     * delete order
     *
     * @param string $deleteId
     * @return bool
     * @throws ActionFailException
     */
    public function delete($deleteId): bool
    {
        DB::beginTransaction();
        try {
            $record = Order::find($deleteId);
            if (is_null($record)) {
                throw new RecordIsNotFoundException();
            }

            $isDeleted = $this->orderRepo->delete($deleteId);
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
     * get order detail
     *
     * @param int|string $orderId
     * @return Order
     * @throws ActionFailException
     */
    public function getOrder($orderId)
    {
        try {
            $order = $this->orderRepo->getSingleObject($orderId);
            if (is_null($order)) {
                throw new RecordIsNotFoundException(ErrorCodes::ERR_RECORD_NOT_FOUND);
            }

            return $order;
        } catch (Exception $e) {
            throw new ActionFailException(
                'getOrder: ' . $orderId,
                null,
                $e
            );
        }
    }

    /**
     * update order status
     *
     * @param array<string,mixed> $data
     * @return bool
     * @throws ActionFailException
     */
    public function updateStatus($data)
    {
        try {
            $this->orderRepo->update([
                'id' => $data['id'],
                'status' => $data['status']
            ]);
            if ($data['status'] == Order::STATUS_CANCELLED) {
                $order = $this->orderRepo->getSingleObject($data['id']);
                foreach ($order->items as $item) {
                    $this->bookRepo->updateStock($item->book_id, -$item->quantity);
                }
            }

            return true;
        } catch (Exception $e) {
            throw new ActionFailException(
                'updateFlagParameters: ' . json_encode($data),
                null,
                $e
            );
        }
    }
}
