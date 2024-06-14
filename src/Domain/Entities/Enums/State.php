<?php

declare(strict_types=1);
namespace App\Domain\Entities\Enums;

enum State: int{
    case  ACTIVE = 0;
    case FINISHED = 1;
    case DELAYED = 2;

}

