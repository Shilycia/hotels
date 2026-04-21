<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'room_id',
        'check_in',
        'check_out',
        'status',
        'total_price',
        'special_request' 
    ];

    // Relasi ke Guest
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // Relasi ke Room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // 🟢 TAMBAHKAN INI: Relasi ke Payment
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}