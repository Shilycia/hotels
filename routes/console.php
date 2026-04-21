<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Booking;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::call(function () {
    
    $expiredBookings = Booking::with('room')
        ->whereIn('status', ['confirmed', 'checked_in'])
        ->where('check_out', '<=', Carbon::now())
        ->get();

    foreach ($expiredBookings as $booking) {
        $booking->update(['status' => 'checked_out']);
        
        if ($booking->room) {
            $booking->room->update(['status' => 'available']);
        }
    }

})->dailyAt('12:00'); 