<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'restaurant_order_id',
        'amount',
        'payment_method',
        'payment_status',
        'note',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function restaurantOrder()
    {
        return $this->belongsTo(RestaurantOrder::class, 'restaurant_order_id');
    }
}