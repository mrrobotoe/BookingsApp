<?php

use App\Bookings\ScheduleAvailability;
use App\Bookings\SlotRangeGenerator;
use App\Models\Employee;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

//Carbon::setTestNow(now()->setTimeFromTimeString('12:00:00'));

Route::get('/', function () {
    $generator = (new SlotRangeGenerator(now()->startOfDay(), now()->addDay()->endOfDay()));

    dd($generator->generate(30));
//    return view('welcome');
});


