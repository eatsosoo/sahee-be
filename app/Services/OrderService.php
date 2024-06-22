<?php

namespace App\Services;

use App\Enums\ErrorCodes;
use App\Exceptions\Business\ActionFailException;
use App\Exceptions\DB\CannotDeleteRecordException;
use App\Exceptions\DB\CannotSaveToDBException;
use App\Exceptions\DB\RecordIsNotFoundException;
use App\Helpers\Common\CommonHelper;
use App\Models\Order;
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

    public function __construct(OrderRepository $orderRepo, OrderItemRepository $orderItemRepo) {
        $this->orderRepo = $orderRepo;
        $this->orderItemRepo = $orderItemRepo;
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

            if (isset($rawConditions['name'])) {
                $param = CommonHelper::escapeLikeQueryParameter($rawConditions['name']);
                $query = $this->orderRepo->queryOnAField(['name', $param]);
            }

            if (isset($rawConditions['user_id'])) {
                $param = CommonHelper::escapeLikeQueryParameter($rawConditions['user_id']);
                $query = $this->orderRepo->queryOnAField(['user_id', $param]);
            }

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