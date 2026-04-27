<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'guest_id', 'room_id', 'check_in_date', 'check_out_date', 
        'total_nights', 'total_amount', 'special_request', 'status'
    ];

    public function guest() { return $this->belongsTo(Guest::class); }
    public function room() { return $this->belongsTo(Room::class); }
    public function payment() { return $this->hasOne(Payment::class); }
}