<?php

use App\Bookings\ServiceSlotAvailability;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Schedule;
use Carbon\Carbon;

it('shows available time slots for a service', function () {
    Carbon::setTestNow(Carbon::parse('1st January 2000'));

    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfDay(),
        ]))
        ->create();

    $service = Service::factory()->create([
        'duration' => 30,
    ]);

    $availability = (new ServiceSlotAvailability(collect([$employee]), $service))
        ->forPeriod(now()->startOfDay(), now()->endOfDay());


    expect($availability->first()->date->toDateString())->toEqual(now()->toDateString());

    expect($availability->first()->slots)->toHaveCount(16);

});

it('lists multiple slots over more than one day', function () {
    Carbon::setTestNow(Carbon::parse('1st January 2000'));

    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfYear(),
        ]))
        ->create();

    $service = Service::factory()->create([
        'duration' => 30,
    ]);

    $availability = (new ServiceSlotAvailability(collect([$employee]), $service))
        ->forPeriod(now()->startOfDay(), now()->addDay()->endOfDay());

    expect($availability->map(fn ($date) => $date->date->toDateString()))
        ->toContain(
            now()->toDateString(),
            now()->addDay()->toDateString()
        );

    expect($availability->first()->slots)->toHaveCount(16);
    expect($availability->get(1)->slots)->toHaveCount(16);
});

it('excludes booked appointments for the employee', function () {
    Carbon::setTestNow(Carbon::parse('1st January 2000'));

    $service = Service::factory()->create([
        'duration' => 30,
    ]);

    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfYear(),
        ]))
        ->has(\App\Models\Appointment::factory()->for($service)->state([
            'starts_at' => now()->setTimeFromTimeString('10:00:00'),
            'ends_at' => now()->setTimeFromTimeString('10:45:00'),
        ]))
        ->create();

    $availability = (new ServiceSlotAvailability(collect([$employee]), $service))
        ->forPeriod(now()->startOfDay(), now()->endOfDay());


    $slots = $availability->map(function (\App\Bookings\Date $date) {
        return $date->slots->map(fn ($slot) => $slot->time->toTimeString());
    })
        ->flatten()
        ->toArray();

    expect($slots)
        ->toContain('11:30:00')
        ->not->toContain('10:00:00')
        ->not->toContain('10:30:00');
});

it('shows multiple employees for a service', function () {
    Carbon::setTestNow(Carbon::parse('1st January 2000'));

    $service = Service::factory()->create([
        'duration' => 30,
    ]);

    $employee = Employee::factory()
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfYear(),
        ]))
        ->has(\App\Models\Appointment::factory()->for($service)->state([
            'starts_at' => now()->setTimeFromTimeString('10:00:00'),
            'ends_at' => now()->setTimeFromTimeString('10:45:00'),
            'cancelled_at' => now(),
        ]))
        ->create();

    $availability = (new ServiceSlotAvailability(collect([$employee]), $service))
        ->forPeriod(now()->startOfDay(), now()->endOfDay());


    $slots = $availability->map(function (\App\Bookings\Date $date) {
        return $date->slots->map(fn ($slot) => $slot->time->toTimeString());
    })
        ->flatten()
        ->toArray();

    expect($slots)
        ->toContain('11:30:00')
        ->toContain('10:00:00')
        ->toContain('10:30:00');
});

it('ignore cancelled appointments', function () {
    Carbon::setTestNow(Carbon::parse('1st January 2000'));

    $service = Service::factory()->create([
        'duration' => 30,
    ]);

      = Employee::factory()
        ->count(2)
        ->has(Schedule::factory()->state([
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->endOfYear(),
        ]))
        ->has(\App\Models\Appointment::factory()->for($service)->state([
            'starts_at' => now()->setTimeFromTimeString('10:00:00'),
            'ends_at' => now()->setTimeFromTimeString('10:45:00'),
            'cancelled_at' => now(),
        ]))
        ->create();

    $availability = (new ServiceSlotAvailability(collect([$employee]), $service))
        ->forPeriod(now()->startOfDay(), now()->endOfDay());

    expect($availability->first()->slots->first()->employees)->toHaveCount(2);
});

