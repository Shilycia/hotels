<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantOrder extends Model
{
    protected $fillable = [
        'guest_id', 'table_or_room', 'order_type', 'total_amount', 'notes', 'status'
    ];

    public function guest() { return $this->belongsTo(Guest::class); }
    public function details() { return $this->hasMany(RestaurantOrderDetail::class); }
    public function payment() { return $this->hasOne(Payment::class); }
}