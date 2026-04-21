<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'room_id',
        'total_price',
        'status',
        'notes'
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function details()
    {
        return $this->hasMany(RestaurantOrderDetail::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}