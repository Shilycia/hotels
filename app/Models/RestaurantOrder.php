<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrder extends Model
{
    use HasFactory;

    // 🟢 Daftarkan kolom baru
    protected $fillable = [
        'guest_id',
        'room_id',
        'total_price',
        'status',
        'notes'
    ];

    // Relasi ke tabel Guest (Tamu)
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // Relasi ke tabel Room (Kamar - In-Room Dining)
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Relasi ke tabel RestaurantOrderDetail (Isi Pesanan)
    // 🟢 Kita beri nama 'details' agar sesuai dengan pemanggilan di Blade
    public function details()
    {
        return $this->hasMany(RestaurantOrderDetail::class);
    }

    // Relasi ke tabel Payment (Tagihan)
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}