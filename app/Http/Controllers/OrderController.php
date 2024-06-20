<?php

namespace App\Http\Controllers;

use App\DataResources\BaseDataResource;
use App\DataResources\OrderResource;
use App\Helpers\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\SearchOrdersRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /** @var OrderService */
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * search orders
     *
     * @param SearchOrdersRequest $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidPaginationInfoException
     */
    public function search(SearchOrdersRequest $request)
    {
        // 1. get validated payload
        $orderData = $request->input();

        // 2. get pagination if any
        $paging = null;
        if (isset($orderData['pagination'])) {
            $paging = $request->getPaginationInfo();
        }

        // 3. Call business processes
        $orderList = $this->orderService->searchOrders($orderData, $paging);

        // 4. Convert result to output resource
        $result = BaseDataResource::generateResources($orderList, OrderResource::class);

        // 5. Send response using the predefined format
        if (is_null($paging)) {
            return ApiResponse::v1()
                ->send($result, 'orders');
        } else {
            return ApiResponse::v1()
                ->withTotalPages($paging->last_page, $paging->total)
                ->send($result, 'orders');
        }
    }

    /**
     * create order
     *
     * @param StoreOrderRequest $request
     * @return Response
     * @throws ActionFailException
     */
    public function create(StoreOrderRequest $request)
    {
        $orderCreated = $this->orderService->create($request->all());
        $result = new OrderResource($orderCreated);

        return ApiResponse::v1()->send($result, dataKey: 'order');
    }

    /**
     * update order
     *
     * @param UpdateOrderRequest $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidModelInstanceException
     */
    public function update(UpdateOrderRequest $request)
    {
        // 1. get validated payload
        $orderData = $request->all();

        // 2. Call business processes
        $mailBlock = $this->orderService->update($orderData);
        $message = __('message.edit_success');

        // 3. Convert result to output resource
        $result = new OrderResource($mailBlock);

        // 4. Send response using the predefined format
        return ApiResponse::v1()->withMessage($message)->send($result, 'order');
    }

    /**
     * delete order
     *
     * @param Request $request
     * @return Response
     * @throws ActionFailException
     */
    public function delete($requestId)
    {
        $result = $this->orderService->delete($requestId);
        $message = __('message.delete_success');
        return ApiResponse::v1()->report($result, $message);
    }

    /**
     * detail order
     *
     * @param Request $request
     * @return Response
     * @throws ActionFailException
     * @throws InvalidModelInstanceException
     */
    public function getOrder(Request $request)
    {
        // 1. Get order template id
        $orderId = $request->id;

        // 2. Call business processes
        $order = $this->orderService->getOrder($orderId);

        // 3. Convert result to output resource
        $result = new OrderResource($order);

        // 4. Send response using the predefined format
        return ApiResponse::v1()->send($result, 'order');
    }
}
