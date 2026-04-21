<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantMenu extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'category',
        'description', 
        'price', 
        'foto_url', 
        'is_available', 
        'prep_time', 
        'calories', 
        'allergens', 
        'serving', 
        'rating'
    ];

    public function orderDetails()
    {
        return $this->hasMany(RestaurantOrderDetail::class);
    }
}