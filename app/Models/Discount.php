<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'name', 
        'code',                   
        'discount_type', 
        'discount_value', 
        'min_transaction_amount', 
        'applicable_to', 
        'is_stackable',          
        'valid_from', 
        'valid_until', 
        'is_active',
        'max_uses',    // [D-01] FIX
        'used_count'   // [D-01] FIX
    ];
}