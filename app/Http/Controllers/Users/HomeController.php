<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;

class HomeController
{
    public function index() 
    {
        $rooms = Room::with('roomType')
                    ->where('status', 'available')
                    ->take(3)
                    ->get();

        $targetRoles = ['kepala pelayan', 'kepala koki', 'administrasi', 'manager'];
        
        $staffs = User::with('role')
                    ->whereHas('role', function($query) use ($targetRoles) {
                        $query->whereIn('name', $targetRoles);
                    })
                    ->take(4) 
                    ->get();

        return view('users.home', compact('rooms', 'staffs'));
    }
}
