<?php

namespace App\Entity\Enums;

use Illuminate\Support\Arr;

enum SubscriptionType: string
{
    case FREE = 'free';
    case PRO = 'pro';
    case ENTERPRISE = 'enterprise';

    public static function randomType()
    {
        return Arr::random([self::FREE, self::PRO, self::ENTERPRISE])->value;
    }
}