<?php

namespace App\Models;

use App\Models\PackageOrder;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['room_type_id', 'name', 'description', 'total_price', 'is_active'];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function packageOrders()
    {
        return $this->hasMany(PackageOrder::class);
    }
}