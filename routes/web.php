<?php

use App\Bookings\ScheduleAvailability;
use App\Bookings\ServiceSlotAvailability;
use App\Bookings\SlotRangeGenerator;
use App\Models\Employee;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Carbon::setTestNow(now()->setTimeFromTimeString('17:00:00'));

Route::get('/', function () {
    $employees = Employee::get();
    $service = Service::first();

    $availability = (new ServiceSlotAvailability($employees, $service))
        ->forPeriod(now()->startOfDay(), now()->addDay()->endOfDay());

    dd($availability->firstAvailableDate());
//    return view('welcome');
});


