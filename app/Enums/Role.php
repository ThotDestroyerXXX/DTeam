<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case GUEST = 'guest';
    case PUBLISHER = 'publisher';
}
