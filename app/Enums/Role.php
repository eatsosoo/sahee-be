<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * SUCCESS,
 * FAIL,
 */
final class RoleCode extends Enum
{
    const Admin = 1;
    const Employee = 2;
    const User = 3;
}
