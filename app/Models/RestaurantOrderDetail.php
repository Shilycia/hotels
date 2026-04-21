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

    // Relasi balik ke Induk Order
    public function order()
    {
        return $this->belongsTo(RestaurantOrder::class, 'restaurant_order_id');
    }

    // Relasi ke tabel Menu untuk mengambil nama makanan, foto, dll
    public function menu()
    {
        return $this->belongsTo(RestaurantMenu::class, 'restaurant_menu_id');
    }
}