<?php

declare(strict_types=1);
namespace App\Domain\Entities;

enum State: string{
    case  ACTIVE = 'A';
    case FINISHED = 'F';
    case DELAYED = 'D';

}

