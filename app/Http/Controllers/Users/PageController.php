<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Guest;
use App\Models\Package;
use App\Models\Payment;
use App\Models\RestaurantMenu;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        $activeDiscounts = Discount::where('is_active', true)
            ->whereDate('valid_until', '>=', now())
            ->get();

        $featuredRooms = RoomType::limit(3)->get();

        $featuredMenus = RestaurantMenu::where('is_available', true)->limit(4)->get();

        $packages = Package::with('roomType')->where('is_active', true)->limit(3)->get();

        $staffs = User::with('role')->limit(4)->get();
        
        return view('users.pages.home', compact(
            'activeDiscounts', 'featuredRooms', 'featuredMenus', 'packages', 'staffs'
        ));
    }

    public function about()
    {
        return view('users.pages.about');
    }

    public function roomCatalog(Request $request)
    {
        $query = RoomType::query();
        
        if ($request->filled('adults')) {
            $query->where('adult_capacity', '>=', $request->adults);
        }
        if ($request->filled('children')) {
            $query->where('child_capacity', '>=', $request->children);
        }

        $roomTypes = $query->get();
        return view('users.pages.rooms', compact('roomTypes'));
    }

    public function roomDetail(RoomType $roomType)
    {
        return view('users.pages.room_detail', compact('roomType'));
    }

    public function menuCatalog()
    {
        $menus = RestaurantMenu::where('is_available', true)->get()->groupBy('category');
        return view('users.pages.restaurant', compact('menus'));
    }

    // ==========================================
    // AREA PRIVAT TAMU (WAJIB LOGIN)
    // ==========================================

    public function profile()
    {
        $guestId = session('guest_id');
        $guest = Guest::with([
            'bookings.room.roomType', 
            'restaurantOrders.details.menu', 
            'packageOrders.package'
        ])->findOrFail($guestId);

        return view('users.pages.profile', compact('guest'));
    }

    public function checkoutRoom(Request $request)
    {
        $roomTypeId = $request->room_type_id;
        $roomType = RoomType::findOrFail($roomTypeId);
        
        return view('users.pages.checkout_room', compact('roomType'));
    }

    public function checkoutRestaurant(Request $request)
    {
        return view('users.pages.checkout_restaurant');
    }

    public function customizePackage(Package $package)
    {
        $menus = RestaurantMenu::where('is_available', true)->get();
        return view('users.pages.customize_package', compact('package', 'menus'));
    }

    public function invoice(Payment $payment)
    {
        $guestId = session('guest_id');
        $isOwner = false;

        if ($payment->booking && $payment->booking->guest_id == $guestId) $isOwner = true;
        if ($payment->restaurantOrder && $payment->restaurantOrder->guest_id == $guestId) $isOwner = true;
        if ($payment->packageOrder && $payment->packageOrder->guest_id == $guestId) $isOwner = true;

        if (!$isOwner) {
            abort(403, 'Akses ditolak.');
        }

        return view('users.pages.invoice', compact('payment'));
    }

    public function editProfile()
    {
        $guestId = session('guest_id');
        $guest = Guest::findOrFail($guestId);

        return view('users.pages.edit_profile', compact('guest'));
    }

    
}