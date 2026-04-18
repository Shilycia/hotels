<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengizinkan kolom diisi
    protected $fillable = [
        'name',
        'price',
        'description'
    ];

    /**
     * Relasi ke model Room (Satu tipe kamar punya banyak nomor kamar)
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}