<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = [
            [
                'name' => 'Early Bird Booking',
                'code' => 'EARLYBIRD25',
                'discount_type' => 'percentage',
                'discount_value' => 25,
                'min_transaction_amount' => 500000,
                'max_uses' => 50,
                'is_stackable' => true,
                'applicable_to' => 'bookings',
                'is_active' => true,
                'valid_from' => Carbon::now()->subMonth(),
                'valid_until' => Carbon::now()->addMonths(3),
            ],
            [
                'name' => 'Restaurant Combo',
                'code' => 'RESTORAN10K',
                'discount_type' => 'fixed_amount',
                'discount_value' => 10000,
                'min_transaction_amount' => 50000,
                'max_uses' => 100,
                'is_stackable' => false,
                'applicable_to' => 'restaurant_orders',
                'is_active' => true,
                'valid_from' => Carbon::now()->subMonth(),
                'valid_until' => Carbon::now()->addMonths(2),
            ],
            [
                'name' => 'Package Family',
                'code' => 'FAMILYPAKET15',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'min_transaction_amount' => 750000,
                'max_uses' => 30,
                'is_stackable' => true,
                'applicable_to' => 'package_orders',
                'is_active' => true,
                'valid_from' => Carbon::now()->subMonth(),
                'valid_until' => Carbon::now()->addMonths(3),
            ],
            [
                'name' => 'Auto Weekend Discount',
                'code' => null,
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_transaction_amount' => 0,
                'max_uses' => null,
                'is_stackable' => true,
                'applicable_to' => 'all',
                'is_active' => true,
                'valid_from' => Carbon::now()->subMonth(),
                'valid_until' => Carbon::now()->addYears(1),
            ],
        ];

        foreach ($discounts as $data) {
            Discount::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}

