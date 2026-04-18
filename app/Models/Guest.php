<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'identity_number', 
        'name', 
        'email', 
        'phone', 
        'address'
    ];
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function restaurantOrders()
    {
        return $this->hasMany(RestaurantOrder::class);
    }
}
