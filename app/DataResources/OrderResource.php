<?php

namespace App\DataResources;

use App\Helpers\Common\CommonHelper;
use App\Models\Order;

class OrderResource extends BaseDataResource
{

    /**
     * @var string
     */
    public $user_id;

    /**
     * @var string
     */
    public $user_name;

    /**
     * @var string
     */
    public $user_phone;

    /**
     * @var string
     */
    public $order_code;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    public $total_amount;

    /**
     * @var int
     */
    public $payment_method;

    /**
     * @var int
     */
    public $shipping_address;

    /**
     * @var int
     */
    public $shipping_cost;

    /**
     * @var array
     */
    public $items;

    /**
     * @var string
     */
    public $created_at;

    /**
     * @var string
     */
    public $updated_at;

    public function modelClass(): string
    {
        return Order::class;
    }

    /**
     * @var array|string[]
     */
    protected array $fields = [
        'id',
        'user_id',
        'user_name',
        'user_phone',
        'order_code',
        'status',
        'total_amount',
        'payment_method',
        'shipping_address',
        'shipping_cost',
        'items',
        'created_at',
        'updated_at',
    ];

    public function load(mixed $object): void
    {
        parent::copy($object, $this->fields);
        $this->user_name = $object->user->name;
        $this->user_phone = $object->user->phone;
        $this->items = BaseDataResource::generateResources($object->items, OrderItemResource::class);
        $this->created_at = CommonHelper::formatDate($object->created_at);
        $this->updated_at = CommonHelper::formatDate($object->updated_at);
    }
}
