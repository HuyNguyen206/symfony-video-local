<?php

namespace App\Entity\Enums;

enum SubscriptionPrice:int
{
 case FREE = 0;
 case PRO = 15;
 case ENTERPRISE = 29;
}