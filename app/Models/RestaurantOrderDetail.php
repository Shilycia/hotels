<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantOrderDetail extends Model
{
    public $timestamps = false; 
    protected $fillable = [
        'restaurant_order_id',
        'restaurant_menu_id',
        'quantity',
        'unit_price',
        'subtotal',
        'notes'
    ];

    public function order() { return $this->belongsTo(RestaurantOrder::class); }
    public function menu() 
    { 
        return $this->belongsTo(RestaurantMenu::class, 'restaurant_menu_id'); 
    }
}