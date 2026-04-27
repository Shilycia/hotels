<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageOrderMeal extends Model
{
    protected $fillable = [
        'package_order_id', 'restaurant_menu_id', 'meal_time', 'date', 'quantity'
    ];

    public function packageOrder() { return $this->belongsTo(PackageOrder::class); }
    public function menu() { return $this->belongsTo(RestaurantMenu::class); }
}