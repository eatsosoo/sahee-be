<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    const PRODUCT_LIST = [
        'id' => 1,
        'name' => 'product_list'
    ];
    const PRODUCT_CREATE = [
        'id' => 2,
        'name' => 'product_create'
    ];
    const PRODUCT_READ = [
        'id' => 3,
        'name' => 'product_read'
    ];
    const PRODUCT_UPDATE = [
        'id' => 4,
        'name' => 'product_update'
    ];
    const PRODUCT_DELETE = [
        'id' => 5,
        'name' => 'product_delete'
    ];
    const CATEGORY_LIST = [
        'id' => 6,
        'name' => 'category_list'
    ];
    const CATEGORY_CREATE = [
        'id' => 7,
        'name' => 'category_create'
    ];
    const CATEGORY_READ = [
        'id' => 8,
        'name' => 'category_read'
    ];
    const CATEGORY_UPDATE = [
        'id' => 9,
        'name' => 'category_update'
    ];
    const CATEGORY_DELETE = [
        'id' => 10,
        'name' => 'category_delete'
    ];
    const ORDER_LIST = [
        'id' => 11,
        'name' => 'order_list'
    ];
    const ORDER_CREATE = [
        'id' => 12,
        'name' => 'order_create'
    ];
    const ORDER_READ = [
        'id' => 13,
        'name' => 'order_read'
    ];
    const ORDER_UPDATE = [
        'id' => 14,
        'name' => 'order_update'
    ];
    const ORDER_DELETE = [
        'id' => 15,
        'name' => 'order_delete'
    ];
    const ORDER_CANCEL = [
        'id' => 16,
        'name' => 'order_cancel'
    ];
    const COMMENT_LIST = [
        'id' => 17,
        'name' => 'comment_list'
    ];
    const COMMENT_CREATE = [
        'id' => 18,
        'name' => 'comment_create'
    ];
    const COMMENT_READ = [
        'id' => 19,
        'name' => 'comment_read'
    ];
    const COMMENT_UPDATE = [
        'id' => 20,
        'name' => 'comment_update'
    ];
    const COMMENT_DELETE = [
        'id' => 21,
        'name' => 'comment_delete'
    ];


    const PERMISSION_LIST = [
        // product
        self::PRODUCT_LIST,
        self::PRODUCT_CREATE,
        self::PRODUCT_READ,
        self::PRODUCT_UPDATE,
        self::PRODUCT_DELETE,
        // category
        self::CATEGORY_LIST,
        self::CATEGORY_CREATE,
        self::CATEGORY_READ,
        self::CATEGORY_UPDATE,
        self::CATEGORY_DELETE,
        // order
        self::ORDER_LIST,
        self::ORDER_CREATE,
        self::ORDER_READ,
        self::ORDER_UPDATE,
        self::ORDER_DELETE,
        self::ORDER_CANCEL,
        // comment
        self::COMMENT_LIST,
        self::COMMENT_CREATE,
        self::COMMENT_READ,
        self::COMMENT_UPDATE,
        self::COMMENT_DELETE,
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
