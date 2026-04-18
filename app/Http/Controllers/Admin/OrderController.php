<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan untuk dikelola oleh Admin
     */
    public function index()
    {
        // Mengambil data order, direlasikan dengan data tamu (guest)
        // dan detail pesanannya (details.menu) agar nama makanan bisa ditampilkan
        $orders = RestaurantOrder::with(['guest', 'details.menu'])
                                 ->orderBy('created_at', 'desc')
                                 ->get(); // Gunakan paginate(15) jika data sudah sangat banyak

        return view('admin.order.index', compact('orders'));
    }

    /**
     * Memperbarui status pesanan (diakses dari Modal Update Status)
     */
    public function update(Request $request, RestaurantOrder $order)
    {
        // Validasi ketat agar status yang masuk hanya 'ordered' atau 'paid'
        // sesuai dengan enum di file migration kamu.
        $request->validate([
            'status' => 'required|in:ordered,paid',
        ]);

        // Simpan perubahan status ke database
        $order->update([
            'status' => $request->status
        ]);

        return redirect()->route('admin.orders')->with('success', 'Status pesanan restoran berhasil diperbarui!');
    }

    /**
     * Menghapus data pesanan restoran
     */
    public function destroy(RestaurantOrder $order)
    {
        // Berkat fungsi onDelete('cascade') di migration restaurant_order_details,
        // saat kita menghapus order utama, semua detail item yang dipesan akan otomatis terhapus.
        $order->delete();
        
        return redirect()->route('admin.orders')->with('success', 'Data pesanan berhasil dihapus permanen.');
    }
}