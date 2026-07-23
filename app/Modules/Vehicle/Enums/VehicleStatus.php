<?php

namespace App\Modules\Vehicle\Enums;

enum VehicleStatus:string
{
    case Available = 'available';
    case Booked = 'booked';
    case Maintenance = 'maintenance';
    case Inactive = 'inactive';
}