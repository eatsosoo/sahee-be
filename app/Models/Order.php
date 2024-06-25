<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_code',
        'status',
        'total_amount',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'shipping_cost',
        'payment_method'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_code = self::generateUniqueOrderCode();
        });
    }

    private static function generateUniqueOrderCode()
    {
        do {
            // Generate a random string
            $orderCode = Str::upper(Str::random(6));
        } while (self::where('order_code', $orderCode)->exists());

        return $orderCode;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
}
