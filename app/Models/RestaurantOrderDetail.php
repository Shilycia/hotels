<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantOrderDetail extends Model
{
    public function order()
    {
        return $this->belongsTo(RestaurantOrder::class, 'restaurant_order_id');
    }

    public function menu()
    {
        return $this->belongsTo(RestaurantMenu::class, 'restaurant_menu_id');
    }
}
