<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';

    case MANAGER = 'manager';
    case EMPLOYEE = 'employee';
    case CLIENT = 'client';

    case GUEST = 'guest';
}
