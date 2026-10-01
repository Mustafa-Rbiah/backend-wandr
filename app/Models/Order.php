<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'full_name', 
        'email',
        'phone',
        'address',
        'is_gift',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'gift_message',
        'note',
        'total_price',
        'status',
    ];

    /**
     * Get the order items for the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id');
    }
}
