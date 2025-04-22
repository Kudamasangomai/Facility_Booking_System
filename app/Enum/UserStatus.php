<?php

namespace App\Enum;

enum UserStatus : string
{
    case Active = 'active';
    case NotActive = 'notactive';
   

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
