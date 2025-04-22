<?php

namespace App\Enum;

enum AvailabilityStatus : string
{
    case Free = 'free';
    case Booked = 'booked';
   

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
   
    }
}
