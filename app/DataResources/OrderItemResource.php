<?php

namespace App\DataResources;

use App\Helpers\Common\CommonHelper;
use App\Models\Order;
use App\Models\OrderItem;

class OrderItemResource extends BaseDataResource
{

    /**
     * @var string
     */
    public $order_id;

    /**
     * @var string
     */
    public $book_id;

    /**
     * @var string
     */
    public $book_name;

    /**
     * @var string
     */
    public $book_cover_url;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var int
     */
    public $price;

    /**
     * @var int
     */
    public $total;

    public function modelClass(): string
    {
        return OrderItem::class;
    }

    /**
     * @var array|string[]
     */
    protected array $fields = [
        'order_id',
        'book_id',
        'book_name',
        'book_cover_url',
        'quantity',
        'price',
        'total',
    ];

    public function load(mixed $object): void
    {
        parent::copy($object, $this->fields);
        $this->book_name = $object->book->name;
        $this->book_cover_url = $object->book->book_cover_url;
    }
}
