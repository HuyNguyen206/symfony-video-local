<?php

namespace App\Entity\Enums;

use Symfony\Component\HttpFoundation\Response;

enum InteractiveType:int
{
    case IS_LIKE = 1;
    case IS_DISLIKE = 0;

}