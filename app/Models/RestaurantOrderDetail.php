<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_order_id',
        'restaurant_menu_id',
        'quantity',
        'price'
    ];

    public function order()
    {
        return $this->belongsTo(RestaurantOrder::class, 'restaurant_order_id');
    }

    public function menu()
    {
        return $this->belongsTo(RestaurantMenu::class, 'restaurant_menu_id');
    }
}