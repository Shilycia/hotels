<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'restaurant_order_id', 'package_order_id', 'amount', 
        'discount_applied', 'payment_method', 'payment_status', 
        'midtrans_order_id', 'midtrans_transaction_id', 'paid_at'
    ];

    // Relasi ke berbagai tipe transaksi
    public function booking() { return $this->belongsTo(Booking::class); }
    public function restaurantOrder() { return $this->belongsTo(RestaurantOrder::class); }
    public function packageOrder() { return $this->belongsTo(PackageOrder::class); }
}