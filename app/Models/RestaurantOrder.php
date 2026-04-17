<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantOrder extends Model
{
    public function guest()
    {
        return $this->belongsTo(Guest::class);
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
