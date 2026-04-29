<?php

namespace App\Models;

use App\Models\PackageOrder;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['room_type_id', 'restaurant_menu_id', 'name', 'description', 'total_price', 'is_active'];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function packageOrders()
    {
        return $this->hasMany(PackageOrder::class);
    }

    public function restaurantMenu()
    {
        return $this->belongsTo(RestaurantMenu::class, 'restaurant_menu_id');
    }

    public function paketItems()
    {
        return $this->belongsToMany(
            RestaurantMenu::class,
            'paket_menu_items', 
            'paket_id',         
            'menu_id'           // <-- FIX: Ubah bagian ini menjadi menu_id
        )->withPivot('quantity');
    }
}