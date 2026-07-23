<?php

namespace App\Modules\Booking\Enums;


enum BookingStatus:string
{

    case PENDING='pending';

    case APPROVED='approved';

    case REJECTED='rejected';

    case CANCELLED='cancelled';

    case TRIP_CREATED='trip_created';

    case COMPLETED='completed';

}