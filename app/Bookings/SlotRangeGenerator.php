<?php

namespace App\Bookings;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SlotRangeGenerator
{
    public function __construct(
        protected Carbon $startsAt,
        protected Carbon $endsAt,
    )
    {
        //
    }

    public function generate(int $interval)
    {
        $collection = collect();

        $days = CarbonPeriod::create($this->startsAt, '1 day', $this->endsAt);

        foreach ($days as $day) {
            $collection->push($day);
        }

        return $collection;
    }
}
