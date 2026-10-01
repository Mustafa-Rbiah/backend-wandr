<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'transaction_id', 'method', 'amount', 'currency', 'status'];

    public function order() {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
