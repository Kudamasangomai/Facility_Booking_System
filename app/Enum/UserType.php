<?php

namespace App\Enum;

enum UserType: string
{
    case Customer = 'customer';
    case Staff = 'staff';
    case Admin = 'admin';

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
