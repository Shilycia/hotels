<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'identity_number',
        'address',
        'photo_url', 
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
