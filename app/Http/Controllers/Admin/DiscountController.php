<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::latest()->paginate(10);
        return view('admin.discounts.index', compact('discounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'min_transaction_amount' => 'nullable|numeric|min:0',
            'applicable_to' => 'required|in:bookings,restaurant_orders,package_orders,all',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'is_active' => 'required|boolean',
        ]);

        Discount::create($request->all());
        return redirect()->route('admin.discounts.index')->with('success', 'Promo/Diskon berhasil ditambahkan!');
    }

    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'min_transaction_amount' => 'nullable|numeric|min:0',
            'applicable_to' => 'required|in:bookings,restaurant_orders,package_orders,all',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'is_active' => 'required|boolean',
        ]);

        $discount->update($request->all());
        return redirect()->route('admin.discounts.index')->with('success', 'Promo/Diskon berhasil diperbarui!');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('admin.discounts.index')->with('success', 'Promo/Diskon berhasil dihapus!');
    }
}