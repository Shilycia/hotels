<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantMenu extends Model
{
    public function orderDetails()
    {
        return $this->hasMany(RestaurantOrderDetail::class);
    }
}
