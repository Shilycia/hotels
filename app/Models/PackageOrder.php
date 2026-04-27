<?php

namespace App\Models;

use App\Models\PackageOrderMeal;
use Illuminate\Database\Eloquent\Model;

class PackageOrder extends Model
{
    protected $fillable = ['guest_id', 'package_id', 'start_date', 'end_date', 'total_amount', 'status'];

    public function guest() { return $this->belongsTo(Guest::class); }
    public function package() { return $this->belongsTo(Package::class); }
    public function meals() { return $this->hasMany(PackageOrderMeal::class); }
    public function payment() { return $this->hasOne(Payment::class); }
}
