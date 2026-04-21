<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengizinkan mass assignment
    protected $fillable = [
        'room_number',
        'room_type_id',
        'floor',
        'status'
    ];

    /**
     * Relasi ke model RoomType (Setiap kamar punya satu tipe)
     */

    // Tambahkan ini di dalam class Room jika belum ada
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}