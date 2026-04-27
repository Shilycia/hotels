<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'name', 'discount_type', 'discount_value', 'min_transaction_amount', 
        'applicable_to', 'valid_from', 'valid_until', 'is_active'
    ];
}