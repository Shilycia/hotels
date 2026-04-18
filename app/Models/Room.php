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
        'status'
    ];

    /**
     * Relasi ke model RoomType (Setiap kamar punya satu tipe)
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}