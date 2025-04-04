<?php

namespace App\Enum;

enum SubscriptionType: int
{
    case FREE = 1;
    case PREMIUM = 2;

    public function label(): string
    {
        return match($this) {
            self::FREE => 'Free',
            self::PREMIUM => 'Premium',
        };
    }

    public static function choices(): array
    {
        return [
            self::FREE->label() => self::FREE->value,
            self::PREMIUM->label() => self::PREMIUM->value,
        ];
    }
}