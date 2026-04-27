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
            'name'                   => 'required|string|max:255',
            'code'                   => 'nullable|string|max:50|unique:discounts,code', // Kode harus unik
            'discount_type'          => 'required|in:percentage,fixed_amount',
            'discount_value'         => 'required|numeric|min:0',
            'min_transaction_amount' => 'nullable|numeric|min:0',
            'applicable_to'          => 'required|in:bookings,restaurant_orders,package_orders,all',
            'is_stackable'           => 'required|boolean', // Validasi stackable
            'valid_from'             => 'required|date',
            'valid_until'            => 'required|date|after_or_equal:valid_from',
            'is_active'              => 'required|boolean',
        ]);

        $data = $request->all();
        // Ubah kode menjadi huruf besar semua, jika kosong jadikan null (sebagai promo otomatis)
        $data['code'] = $request->filled('code') ? strtoupper($request->code) : null;

        Discount::create($data);
        return redirect()->route('admin.discounts.index')->with('success', 'Promo/Voucher berhasil ditambahkan!');
    }

    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            // Saat update, abaikan ID diskon ini dari pengecekan unik agar tidak error saat disimpan ulang
            'code'                   => 'nullable|string|max:50|unique:discounts,code,' . $discount->id,
            'discount_type'          => 'required|in:percentage,fixed_amount',
            'discount_value'         => 'required|numeric|min:0',
            'min_transaction_amount' => 'nullable|numeric|min:0',
            'applicable_to'          => 'required|in:bookings,restaurant_orders,package_orders,all',
            'is_stackable'           => 'required|boolean', // Validasi stackable
            'valid_from'             => 'required|date',
            'valid_until'            => 'required|date|after_or_equal:valid_from',
            'is_active'              => 'required|boolean',
        ]);

        $data = $request->all();
        // Ubah kode menjadi huruf besar semua, jika kosong jadikan null
        $data['code'] = $request->filled('code') ? strtoupper($request->code) : null;

        $discount->update($data);
        return redirect()->route('admin.discounts.index')->with('success', 'Promo/Voucher berhasil diperbarui!');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('admin.discounts.index')->with('success', 'Promo/Voucher berhasil dihapus!');
    }
}