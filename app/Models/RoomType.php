<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengizinkan kolom diisi
    protected $fillable = [
        'name', 'price', 'adult_capacity', 'child_capacity', 'description', 'foto', 'rating', 'bed_type', 'bath_count'
    ];

    /**
     * Relasi ke model Room (Satu tipe kamar punya banyak nomor kamar)
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}