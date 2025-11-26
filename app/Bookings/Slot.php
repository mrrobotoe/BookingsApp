<?php

namespace App\Bookings;

use App\Models\Employee;
use Carbon\Carbon;

class Slot
{
    public array $employees = [];
    public function __construct(public Carbon $time)
    {

    }

    public function addEmployee(Employee $employee): void
    {
        $this->employees[] = $employee;
    }

    public function hasEmployees(): bool
    {
        return !empty($this->employees);
    }

}
