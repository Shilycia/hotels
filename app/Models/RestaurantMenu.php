<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantMenu extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'category', 'description', 'price', 'foto_url', 
        'is_available', 'prep_time', 'calories', 'allergens', 
        'serving', 'rating', 'can_bundle_with_room'
    ];

    public function orderDetails()
    {
        return $this->hasMany(RestaurantOrderDetail::class);
    }

    // RELASI PAKET: Jika menu ini adalah "Paket", ambil daftar menu di dalamnya
    public function paketItems()
    {
        return $this->belongsToMany(RestaurantMenu::class, 'paket_menu_items', 'paket_id', 'menu_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}